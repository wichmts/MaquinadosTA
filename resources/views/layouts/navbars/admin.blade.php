<style media="screen">
  .sidebar-mini-icon{
      font-size: 17px !important;
      padding-top: 4px
  }
</style>



<script>
document.addEventListener("DOMContentLoaded", function() {
    var urlActual = window.location.href;
    var match = urlActual.match(/\/\/[^\/]+\/([^\/]+)/);
    var palabraClave = match ? match[1] : null;

    if (palabraClave) {
        var elemento = document.getElementById(palabraClave);
        if (elemento) {
            elemento.style.backgroundColor = "rgb(1100 33 33)";
            elemento.style.marginRight = "11px";
            elemento.style.marginLeft = "-10px";
            elemento.style.paddingLeft = "10px";
            elemento.style.borderRadius = "0px 30px 30px 0px";
        } else {
            console.log("No se encontró ningún elemento con el ID:", palabraClave);
        }
    } else {
        console.log("No se encontró ninguna palabra clave en la URL.");
    }
});


</script>
