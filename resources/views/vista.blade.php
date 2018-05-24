<!DOCTYPE html>
<html>
<head>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
</head>
<body>
 <br /><br />

 
  <input type="text" name="country" id="country" class="form-control input-lg" autocomplete="off" placeholder="Type Country Name" />
 

<script>
$(document).ready(function(){

$('#country').typeahead({
 source: function(query, result)
 {
  $.ajax({
   url:"https://reserveahora.herokuapp.com/api/v1/cities",
   method:"GET",
   data:{query:query},
   dataType:"json",
   success:function(data)
   {
    result($.map(data, function(item){
     return item;
    }));
   }
  })
 }
});

});
</script>


</body>
</html>







<!-- <html>
<head>
 <title>Live search in laravel using AJAX</title>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
 


     <input type="text" name="search" id="search"  />
    
   
        <span id="total_records"></span>
           <br>
       
         <span id="result"></span>

      
<script>
$(document).ready(function(){

fetch_customer_data();

function fetch_customer_data(query = '')
{
 $.ajax({
  url:"http://localhost:8000/api/v1/cities",
  method:'GET',
  data:{query:query},
  dataType:'json',
  success:function(data)
  {
//alert('you selected:' + data.value+','+ data.data);
   $('#result').text(data.result);
   $('#total_records').text(data.total_data);

   ('#search').text(data.result);

  }
 });
}

$(document).on('keyup', '#search', function(){
 var query = $(this).val();
 fetch_customer_data(query);
});
});
</script>
</body>
</html> -->


