
function validarFechas() {
  var fechaInicio = document.getElementById("fecha_inicio").value;
  var fechaFinal = document.getElementById("fecha_final").value;

  if (new Date(fechaFinal) <= new Date(fechaInicio)) {
    alert("La fecha final debe ser mayor que la fecha inicial.");
    return false;
  }
  return true;
}
