<!DOCTYPE HTML>
<html>
<head>

   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>prueba</title>

  <!-- Latest compiled and minified CSS -->
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">


</head>
<body>

<div class="container">


  <!-- "hotel_id": "1991619",
   "nombre_hotel": "Soy Local",
   "direccion": "Carrera 34 8a-24, El Poblado, 050022 Medellín, Colombia",
   "descripcion": "Está en nuestra selección para Medellín.El Soy Local tiene barbacoa y vistas al jardín y está situado en el barrio El Poblado de Medellín, a 300 metros del parque Lleras Las habitaciones están equipadas con aire acondicionado, TV de pantalla plana con canales por cable, cafetera y baño privado con ducha o bañera. Algunas disponen de zona de estar. Además, las habitaciones superiores incluyen bañera de hidromasaje La recepción abre las 24 horas El Soy Local se encuentra a 700 metros del parque El Poblado y a 3,6 km del Pueblito Paisa. El aeropuerto más cercano es el Olaya Herrera, a 3 km del establecimiento.    El Poblado es una opción genial para los viajeros interesados en el ocio nocturno, la comida y la comida local",
  "checkin": "2018-06-01",
    "checkout": "2018-06-04",
    "tipo_habitacion":"Apartamento Deluxe",
    "precio":"COP 198.000",
    "email":"dev1@marketinghotelero.co",
    "nombre":"Luis Eduardo Lopez Martinez",
    "telefono":"3003417996",
    "nombre_huesped":"luis lopez Martinez" -->


      <form method="post" action="{{url('/booking')}}">
        {{ csrf_field() }}

        <div class="col-md-6">
        <div class="form-group">
          <label for="">Hotel id</label>
          <input type="text" class="form-control" value="1991619"   name="hotel_id">
        </div>
        <div class="form-group">
          <label for="">Nombre Hotel</label>
          <input type="text" class="form-control" value="Soy Local"   name="nombre_hotel">
        </div>
       
       <div class="form-group">
          <label for="">Checkin</label>
          <input type="text" class="form-control" value="2018-07-01"  name="checkin" >
        </div>
         <div class="form-group">
          <label for="">Tipo_habitacion</label>
          <input type="text" class="form-control" value="Apartamento Deluxe"   name="tipo_habitacion">
        </div>

        </div>



     <div class="col-md-6">
        
         <div class="form-group">
          <label for="">Dirección</label>
          <input type="text" class="form-control" value="Carrera 34 8a-24, El Poblado, 050022 Medellín, Colombia"   name="direccion">
        </div>
        <div class="form-group">
          <label for="">Descripcion</label>
          <input type="text" class="form-control" value="Está en nuestra selección para Medellín.El Soy Local tiene barbacoa y vistas al jardín y está situado en el barrio El Poblado de Medellín, a 300 metros del parque Lleras Las habitaciones están equipadas con aire acondicionado, TV de pantalla plana con canales por cable, cafetera y baño privado con ducha o bañera. Algunas disponen de zona de estar. Además, las habitaciones superiores incluyen bañera de hidromasaje La recepción abre las 24 horas El Soy Local se encuentra a 700 metros del parque El Poblado y a 3,6 km del Pueblito Paisa. El aeropuerto más cercano es el Olaya Herrera, a 3 km del establecimiento.    El Poblado es una opción genial para los viajeros interesados en el ocio nocturno, la comida y la comida local"   name="descripcion">
        </div>
     

          <div class="form-group">
          <label for="">checkout</label>
          <input type="text" class="form-control" value="2018-06-04"  name="checkout">
        </div>
        <div class="form-group">
          <label for="">Precio</label>
          <input type="text" class="form-control" value="COP 198.000"   name="precio">
        </div>
     </div>


<div class="col-md-6">
        <div class="form-group">
          <label for="">Email</label>
          <input type="email" class="form-control" value=""  " name="email">
        </div>
        <div class="form-group">
          <label for="">Nombre</label>
          <input type="text" class="form-control" value=""   name="nombre">
        </div>
  </div>
        
     <div class="col-md-6">
         <div class="form-group">
          <label for="">Telefono</label>
          <input type="number" class="form-control" value=""   name="telefono">
        </div>
        <div class="form-group">
          <label for="">Nombre Huesped</label>
          <input type="text" class="form-control" value=""   name="nombre_huesped">
        </div>
        
    </div>

        <button type="submit" class="btn btn-default">Submit</button>

      </form>


  
</div>
<!-- Latest compiled and minified JavaScript -->
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</body>
</html>