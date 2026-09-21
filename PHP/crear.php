<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos</title>
    <link rel="stylesheet" href="../CSS/estilo.css">


    
</head>
<body>
    <main>
        
        <?php include 'menu.php'; ?>
        
        <h1 id="titulocur">Crear nuevo curso</h1>
        <p class="pcur">Completa los detalles de tu curso y visualiza cómo se verá para los estudiantes.</p>


        <div id="contotal">
        
    
            <div id="Crearcurso">
                <form action="../PHP conexiones/crecur.php" method="post" enctype="multipart/form-data">
                <h2 class="Curscre">Título del curso</h2>
                <input type="text" id= "nombrecur" name="Titulo_curso" maxlength="50" placeholder="ej: Desarrollo de paginas webs">

                <h2>Descripción del curso:</h2>
                <textarea id="Desc" name="Descripcion_curso"  rows="8" cols="50"></textarea>

                <label class="Detalle" for="Cursotip">Tipo de curso:</label>
                    <select id="Cursotip" name="Tipo_curso">
                        <option value="Informática">Informática</option>
                        <option value="Programacion">Programación</option>
                        <option value="Arte">Arte</option>
                        <option value="Cocina">Cocina</option>
                        <option value="Psicologia">Psicologia</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Escritura">Escritura</option>
                        <option value="Animaciones">Animaciones</option>
                        <option value="Economia">Economía</option>
                        <option value="Hardware">Hardware</option>
                    </select>
        
                <h2 id="NivCurso">Nivel del Curso:</h2>
                <select id="Cursotip" name="Nivel_Curso">
                    <option value="Bajo">Bajo</option>
                    <option value="Medio">Medio</option>
                    <option value="Alto">Alto</option>
                </select>
                    
                <h2>Duración estimada:</h2> 
                <div id="horarios">  
                    <input type="number" id="curnum" name="Duracion_estimada" min="10" max="100" placeholder="Ej: 12"> 
                    <h2 id="horas">Horas</h2>
                </div> 

                <h2>Precio:</h2> 
                <div id="Plata">  
                    <input type="number" id="curplata" name="Precio" min="1000" max="100000" placeholder="Ej: 10000"> 
                    <h2 id="pesos">Pesos</h2>
                </div> 
                <h2 class="Curscre">Adjunta la imagen de tu curso</h2>
                <input type="file" id="Dataso" name="Dataso" accept="image/*" required />

         
                <div class="btncurso">
                <button type="button" id="Visbut"> Vista previa </button>
  
                <button type="Submit" id="Env"> Publicar Curso </button>
                </div>

                </form>
            </div> 


            <div class="Visprev">
                        <h2 class="Vispr">Vista previa</h2>
                <p class="pcur">Previsualiza tu contenido:</p>
            <div class="contimg">
                <img id="curso" src="../IMG/Cursoejemplo.jpg" alt="Imagen cargada">
            </div>
            <textarea readonly id= "titlecur" name="descripcion" rows="8" cols="50"></textarea>

            <textarea readonly id="curdesc" name="descripcion" rows="8" cols="70"></textarea>

            <div class="infoprev">
                <textarea name="hor" id="hours"></textarea>
                <h2>Horas</h2>

        

            </div>
            <div id="barra">
            </div>
            <div class="infoprev">
            <h2 id="Prec" style="display: none;">Precio</h2>
            <h2 id="peso" style="display: none;"> $ </h2>
                <textarea name="niv" readonly id="precio"></textarea>
            </div>
            </div>

        </div> 
    </main>

    <?php include 'footer.php'; ?>

    <script src="../JS/Botones.js"></script>
</body>
</html>