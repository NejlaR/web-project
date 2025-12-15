let Utils = {
    datatable: function(table_id, columns, data, pageLength = 10){
        if ($.fn.dataTable.isDataTable("#" + table_id)) {
            $("#" + table_id).DataTable().destroy();
        }
        $("#" + table_id).DataTable({
            data,
            columns,
            pageLength
        });
    },

    parseJwt: function(token){
        if (!token) return null;
        try {
            return JSON.parse(atob(token.split('.')[1]));
        } catch(e){
            console.error("Invalid token");
            return null;
        }
    }
}
