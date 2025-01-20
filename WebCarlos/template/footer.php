<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#fecha",{
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
    });
    flatpickr("#fecha_inicio",{
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
    });
    flatpickr("#fecha_final",{
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
    });
    $('.select2').select2();
</script>
    </body>
</html>
<?php mysqli_close($conn);?>