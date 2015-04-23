'use strict'

function getSingleRow(db,table,whereClause,callback) {
	$.getJSON("sql.php",{db:db,sql:"SELECT * FROM " + table + " WHERE " + whereClause + " LIMIT 1"},function(data) {
		var result = {};
		for(var key in data.columns) {
			result[data.columns[key]] = data.rows[0][key];
		}
		callback(result);
	});
}

function EditableTable(options) {

	if(!options.db) {
		alert('Warning: No database selected, using "rpits"');
		options.db = 'rpits';
	}
	if(!options.dbTable) {
		return 'No database table selected';
	}

	this.options = options;

	this.loadTable = function(start,end,where,order) {
		this.start = start;
		this.end = end;
		if (!start)
			this.start = 0;
		if(!end)
			this.end = 30;
		var sql = 'SELECT * FROM ' + options.dbTable;
		if(where)
			sql += ' WHERE ' + where;
		if(order) {
			sql += ' ORDER BY ' + order;
		}
		sql += ' LIMIT ' + start + ', '+ end;
		$.getJSON('sql.php',{db:options.db,sql:sql},this._renderTable.bind(this));
	}

	this._toggleEditable = function(e) {
		if($(e.target).closest('td:not(.editable):not(.action),td.displayFunction').length) return true;
		
		//handle removing from database
		if ($(e.target).hasClass("delete")){
			var id = $(e.target).parent().parent().find($(".id")).text();
			var e_name = $(e.target).parent().parent().find($(".name")).text();
			if (confirm("Are you sure you want to delete event id " + id + " with name: " + e_name)){
				var sql = "DELETE FROM `" + options.db + "`.`events` WHERE `events`.`id` = " + id;
				$(e.target).parent().parent().remove();
				$.getJSON('sql.php',{db:options.db,sql:sql});
			}
		}
		else{
			var row = $(e.currentTarget);
			var cell = $(e.target);
			if(!row.hasClass('editing')) {
				row.addClass('editing');
				row.find('button.action').text('Save');
				this._toggleInputs(row);
				cell.find('input').focus();
				row.find('input, button.action').keydown(function(e) {
					if(e.keyCode == 13) {
						this._toggleInputs(row);
						this._saveRow(row);
						row.find('button.action').text('Edit');
						row.find('button.action').unbind();
						row.removeClass('editing');
						e.stopPropagation()
						row.find('button.cancel').remove();
					}
				}.bind(this));
				var cancelButton = $('<button class="cancel">Cancel</button>');
				cancelButton.click(function(e) {
					this._toggleInputs(row);
					row.find('button.action').text('Edit');
					row.find('button.action').unbind();
					row.removeClass('editing');
					e.stopPropagation()
					row.find('button.cancel').remove();
				}.bind(this));
				row.find('button.action').click(function(e) {
					this._toggleInputs(row);
					this._saveRow(row);
					row.find('button.action').text('Edit');
					row.find('button.action').unbind();
					row.removeClass('editing');
					e.stopPropagation()
					row.find('button.cancel').remove();
				}.bind(this));
				row.find('td.action').append(cancelButton);
			}
		}
	}

	this._addRow = function(e) {
		var row = $(e.currentTarget).closest('tr');
		var columns = '';
		var values = '';
		row.children().each(function() {
			var name = $(this).attr('name');
			var val = $(this).find('input').length > 0 ? $(this).find('input').val() : $(this).text();
			if(name && name != 'id') {
				columns += name + ', ';
				val = val.replace(/'/g, "\\'");
				values += "'" + val + "', ";
			}
		});
		columns = columns.slice(0,-2);
		values = values.slice(0,-2);
		var sql = 'INSERT INTO ' + options.dbTable + ' (' + columns + ') VALUES (' + values + ');';
		var data = new Object();
		data.sql = sql;
		data.db = options.db;
		$.getJSON('sql.php',data,function(d) {
			$('.editableTable').find('tr.erow').last().children().first().html(d.id);
			// ^^ That's not good code ^^
		});
		row.removeClass('nrow');
		row.addClass('erow');
		e.stopPropagation();
		this._toggleInputs(row);
		row.find('button.action').html('Edit');
		row.find('button.action').unbind();
		row.click(this._toggleEditable.bind(this));
		this._addAddRow();
	}

	this._saveRow = function(row) {
		var data = new Object();
		var sql = 'UPDATE ' + options.dbTable + ' SET ';
		var id;
		row.children().each(function() {
			var td = $(this);
			var name = td.attr('name');
			var val = td.find('input').length > 0 ? td.find('input').val() : td.text();
			if(name && name != 'id') {
				val = val.replace(/'/g, "\\'");
				sql += "" + name + "='" + val + "', ";
			} else if(name == 'id') {
				id = val;
			}
		});
		sql = sql.slice(0,-2);
		sql += " WHERE id='" + id + "'";
		data.sql = sql;
		data.db = options.db;
		$.get('sql.php',data);
	}

	this._toggleInputs = function (row) {
		row.children('.editable').each(function () {
			var td = $(this);
			if(td.find('input').length == 0) {
				var value = td.text();
				var classes = td.attr('class');
				td.html($('<input type="text" />').addClass(classes).val(value));
				td.addClass('editing');
			} else {
				td.removeClass('editing');
				if(td.hasClass('displayFunction')) {
					var classes = td.attr('class').split(" ");
					for(var i = 0; i < classes.length;i++) {
						if(options.displayFunction[classes[i]]) {
							td.html(options.displayFunction[classes[i]](td.find('input').val()));
						}
					}
				} else {
					td.html(td.find('input').val());
				}
			}
		});
	}

	this._renderTable = function(data) {
		this.data = data;
		var table = $('<table class="editableTable"></table>');
		this.table = table;
		table.addClass(options.tableClass);
		table.attr('id',options.tableId);
		var headerRow = $('<tr class="header"></tr>');
		if (!options.columnHeaders) {
			options.columnHeaders = data.columns;
		}
		if(options.columnHeaders.length != data.columns.length) {
			return false;
		}
		var i, j;
		for(i = 0; i < options.columnHeaders.length; i++) {
			if (options.hideColumns && options.hideColumns.indexOf(data.columns[i]) != -1)
				continue;
			var th = $("<th></th>");
			th.addClass(data.columns[i]);
			th.text(options.columnHeaders[i]);
			headerRow.append(th);
		}
		headerRow.append('<th class="action">Action</th>')
		table.append(headerRow);
		for(i = 0; i < data.rows.length + 1; i++) {
			var dataRow = $('<tr></tr>');
			if(i < data.rows.length)
				dataRow.addClass('erow');
			else
				dataRow.addClass('nrow'); // new row
			for(j = 0; j < data.rows[0].length; j++) {
				if (options.hideColumns && options.hideColumns.indexOf(data.columns[j]) != -1)
					continue;
				var td = $("<td></td>");
				td.addClass(data.columns[j]);
				if(options.uneditableColumns == null || (options.uneditableColumns && options.uneditableColumns.indexOf(data.columns[j]) == -1)) {
					td.addClass('editable');
				}

				td.attr('name',data.columns[j]);
				if(options.displayFunction && typeof options.displayFunction[data.columns[j]] === 'function' && i < data.rows.length) {
					td.html(options.displayFunction[data.columns[j]](data.rows[i][j]));
					td.addClass('displayFunction');
				} else if(i < data.rows.length) {
					td.text(data.rows[i][j]);
				} else {

				}
				dataRow.append(td);
			}
			if(i < data.rows.length)
				dataRow.append('<td class="action"><button class="action">Edit</button> <button class="delete">Delete</button></td>');
			else
				dataRow.append('<td class="action"><button class="action">Add</button></td>');
			table.append(dataRow);
		}
		this._toggleInputs(table.find('.nrow'));
		table.find('.erow').click(this._toggleEditable.bind(this));
		table.find('.nrow button').click(this._addRow.bind(this));
		table.find('.nrow input').keydown(function(e) {
				if(e.keyCode == 13) {
					this._addRow(e);
				}
		}.bind(this));
		options.element.append(table);
		if (typeof options.callback == 'function') {
			options.callback();
		}
		return true;
	}
	this._addAddRow = function() {
		var dataRow = $('<tr></tr>');
		var data = this.data;
		dataRow.addClass('nrow');
		for(var j = 0; j < data.rows[0].length; j++) {
			if (options.hideColumns && options.hideColumns.indexOf(data.columns[j]) != -1)
				continue;
			var td = $("<td></td>");
			td.addClass(data.columns[j]);
			if(options.uneditableColumns == null || (options.uneditableColumns && options.uneditableColumns.indexOf(data.columns[j]) == -1)) {
				td.addClass('editable');
			}

			td.attr('name',data.columns[j]);
			dataRow.append(td);
		}
		dataRow.append('<td class="action"><button class="action">Add</button></td>');
		this.table.append(dataRow);
		this._toggleInputs(this.table.find('.nrow'));
		this.table.find('.nrow button').click(this._addRow.bind(this));
		this.table.find('.nrow input').keydown(function(e) {
				if(e.keyCode == 13) {
					this._addRow(e);
				}
		}.bind(this));
	}
}


