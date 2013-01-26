function EditableTable(options) {
	
	if(!options.db)
		options.db = 'rpits';
	if(!options.dbTable)
		return 'No database table selected';
	
	this.loadTable = function(start,end,where) {
		this.start = start;
		this.end = end;
		if (!start)
			this.start = 0;
		if(!end)
			this.end = 30;
		var sql = 'SELECT * FROM ' + options.dbTable;
		if(where)
			sql += ' WHERE ' + where;
		sql += ' LIMIT ' + start + ', '+ end;
		$.getJSON('sql.php',{db:options.db,sql:sql},_renderTable);
	}
	
	_toggleEditable = function(e) {
		var row = $(e.currentTarget);
		console.log(row);
		if(!row.hasClass('editing')) {
			row.addClass('editing');
			row.find('button.action').text('Save');
			_toggleInputs(row);
			row.find('input, button.action').keydown(function(e) {
				if(e.keyCode == 13) {
					_toggleInputs(row);
					_saveRow(row);
					row.find('button.action').text('Edit');
					row.find('button.action').unbind();
					row.removeClass('editing');
					e.stopPropagation()
				}
			});
			var cancelButton = $('<button class="cancel">Cancel</button>');
			cancelButton.click(function(e) {
				_toggleInputs(row);
				row.find('button.action').text('Edit');
				row.find('button.action').unbind();
				row.removeClass('editing');
				e.stopPropagation()
				row.find('button.cancel').remove();
			});
			row.find('button.action').click(function(e) {
				_toggleInputs(row);
				_saveRow(row);
				row.find('button.action').text('Edit');
				row.find('button.action').unbind();
				row.removeClass('editing');
				e.stopPropagation()
				row.find('button.cancel').remove();
			});	
			row.find('td.action').append(cancelButton);
		}
	}
	
	_addRow = function(e) {
		var row = $(e.currentTarget).closest('tr');
		var columns = '';
		var values = '';
		row.children().each(function() {
			var name = $(this).attr('name');
			var val = $(this).find('input').length > 0 ? $(this).find('input').val() : $(this).text();
			if(name && name != 'id') {
				columns += name + ', ';
				values += "'" + val + "', ";
			}
		});
		columns = columns.slice(0,-2);
		values = values.slice(0,-2);
		var sql = 'INSERT INTO ' + options.dbTable + ' (' + columns + ') VALUES (' + values + ');';
		var data = new Object();
		data.sql = sql;
		data.db = options.db
		$.getJSON('sql.php',data,function(d) {
			$('.editableTable').find('tr').last().children().first().html(d.id);
			// ^^ That's not good code ^^
		});
		row.removeClass('nrow');
		row.addClass('erow');
		e.stopPropagation();
		_toggleInputs(row);
		row.find('button.action').html('Edit');
		row.find('button.action').unbind();
		row.click(_toggleEditable);
	}
	
	_saveRow = function(row) {
		var data = new Object();
		var sql = 'UPDATE ' + options.dbTable + ' SET ';
		var id;
		row.children().each(function() {
			td = $(this);
			var name = td.attr('name');
			var val = td.find('input').length > 0 ? td.find('input').val() : td.text();
			if(name && name != 'id') {
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
	
	_toggleInputs = function (row) {
		row.children('.editable').each(function () {
			var td = $(this);
			if(td.find('input').length == 0) {
				var value = td.text();
				var klass = td.attr('class');
				td.html($('<input type="text" />').addClass(klass).val(value));
				td.addClass('editing');
			} else {
				td.removeClass('editing');
				td.html(td.find('input').val());
			}
		});
	}

	_renderTable = function(data) {
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
					td.html(options.displayFunction[j].call(data.rows[i][j]));
				} else if(i < data.rows.length) {
					td.text(data.rows[i][j]);
				} else {
					
				}
				dataRow.append(td);
			}
			if(i < data.rows.length)
				dataRow.append('<td class="action"><button class="action">Edit</button></td>');
			else
				dataRow.append('<td class="action"><button class="action">Add</button></td>');
			table.append(dataRow);	
		}
		_toggleInputs(table.find('.nrow'));
		table.find('.erow').click(_toggleEditable);
		table.find('.nrow button').click(_addRow);
		options.element.append(table);
		if (typeof options.callback == 'function') {
			options.callback();
		}
		return true;
	}
}
	