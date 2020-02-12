function SubmitFormData() {
    var dev_id = $("#dev_id").val();
    var type = $("#type").val();
    $.post("convert.php", { dev_id: dev_id, type: type},
    function(data) {
	 $('#wyniki').html(data);
	 $('#test')[0].reset();
    });
}