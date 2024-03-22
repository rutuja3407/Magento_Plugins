function addCustomAttribute(){
    console.log("Appending a Custom Attribute.");
   var val = jQuery("#this_attribute").val();
   var div = generate(jQuery("#this_attribute").val())
    jQuery("#submit_custom_attr").before(div);
    jQuery("#this_attribute").val("");

}

function generate(name){
   var attributeDiv =  jQuery("<div>",{"class":"gm-div","style":"margin-top:18px","id":name+"Div"});
   var labelForAttr = jQuery("<strong>",{"for":name,"style":"margin-left:12px; margin-top:18px"}).text(name);
   var inputAttr = jQuery("<input>",{"id":name,"name":name,"class":"form-control gm-input","style":"margin-left:211px; margin-top:18px; position:absolute; padding:7px","type":"text", "placeholder":"Enter name of IDP attribute"});

   attributeDiv.append(labelForAttr);
   attributeDiv.append(inputAttr);

   return attributeDiv;

}

function deleteCustomAttribute(){
   var val = jQuery("#this_attribute").val();

   if(val.length>1){
       console.log("Deleting mapping for "+val);
       jQuery("#"+val+"Div").remove();
       jQuery("#this_attribute").val("");}
}
