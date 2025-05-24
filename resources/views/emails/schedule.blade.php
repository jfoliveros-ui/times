<div style="font-family: Arial, sans-serif; color: #333; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; max-width: 600px; margin: auto;">
    <h1 style="text-align: center; color: #0044cc;">Confirmación de Asignación de Horario</h1>

    <p>Estimado(a) Docente,</p>

    <p>Se le ha asignado la siguiente asignatura:</p>

    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #0044cc;">
        <p><strong>Centro de Tutoría:</strong> {{ $data['cetap'] }}</p>
        <p><strong>Asignatura:</strong> {{ $data['subject'] }}</p>
        <p><strong>Jornada:</strong> {{ $data['working_day'] }}</p>
        <p><strong>Fechas:</strong> {{ $data['dates'] }}</p>
        <p><strong>Modalidad:</strong> {{ $data['mode'] }}</p>
    </div>

    <p>Por favor ingrese al sistema Time para aceptar la asignación.</p>

    <link rel="stylesheet" href="http://127.0.0.1:8000/admin/login">

    <p>Atentamente,<br><strong style="color:#0044cc;">Registro y Control</strong></p>
</div>
