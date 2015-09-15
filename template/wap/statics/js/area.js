function loadArea(areaId,areaType) {
    $.post(ajaxurl,{'areaId':areaId},function(data){
        if(areaType=='city'){
           $('#'+areaType).html('<option value="-1">选择城市</option>');
           $('#district').html('<option value="-1">选择地区</option>');
        }else if(areaType=='district'){
           $('#'+areaType).html('<option value="-1">选择地区</option>');
        }
        if(areaType!='null'){
            $.each(data,function(no,items){
                $('#'+areaType).append('<option value="'+items.area_id+'">'+items.area_name+'</option>');
            });
        }
    });
}