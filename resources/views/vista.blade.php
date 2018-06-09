<html lang="en">


<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>


 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
 <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
 <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
</head>


<body>

 <select class="itemName form-control" style="width:500px;" name="itemName" >
     
 </select>


<script type="text/javascript">

     $('.itemName').select2({
       placeholder: 'Selecciona ciudad',
       ajax: {
         url: 'https://reserveahora.herokuapp.com/api/v1/cities',
         dataType: 'json',
         delay: 250,
         processResults: function (data) {
           return {
             results:  $.map(data, function (item) {
                   return {
                    
                       text: item.city,
                       id: item.id,
                       
                   }
               })
           };
         },
         cache: true
       }
     });


</script>


</body>
</html>















<!-- <!DOCTYPE html>
<html>
<head>
   <title>Autocomplete Vue js using Laravel</title>
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" >
   <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.min.js"></script>


   <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
</head>
<body>


<div class="container" id="app">
   <div class="row">
       <div class="col-sm-8">
           <h1>Autocomplete Vue js using Laravel</h1>
           <div class="panel panel-default">
               <div class="panel-heading">Please Enter for Search</div>
               <div class="panel-body">
                   <autocomplete></autocomplete>
               </div>
           </div>
       </div>
   </div>
</div>


<script type="text/javascript">


   Vue.component('autocomplete', {
     template: '<div><input type="text" placeholder="what are you looking for?" v-model="searchquery" v-on:keyup="autoComplete" class="form-control"><div class="panel-footer" v-if="data_results.length"><ul class="list-group"><li class="list-group-item" v-for="result in data_results"> <a href="#"> @{{ result.city }}     @{{ result.id }} </a></li></ul></div></div>',
     data: function () {
       return {
         searchquery: '',
         data_results: []
       }
     },
     methods: {
       autoComplete(){
       this.data_results = [];
       if(this.searchquery.length > 1){
        axios.get('http://8aef1f94.ngrok.io/api/v1/cities',{params: {searchquery: this.searchquery}}).then(response => {
           console.log(response);
             this.data_results = response.data;

             
        });
       }
      }
     },
   })


   const app = new Vue({
       el: '#app'
   });
</script>


</body>
</html>
 -->




























<!-- <!DOCTYPE html>
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
   url:'http://253a4510.ngrok.io/cities',
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
 -->






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


