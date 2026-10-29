<?php
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPanel = (strpos($scriptPath, '/PHPAdmin/') !== false);
$phpBase = $isAdminPanel ? '../' : '';
?>

<script src="<?php echo $isAdminPanel ? '../../JS/PreguntasFrecuentes.js' : '../JS/PreguntasFrecuentes.js'; ?>" defer></script>
<footer class="footerMain">
    <div class="footerNombre">
        <div class="footerLogo">
            <h2>Aprendomo</h2>
        </div>
        <p>
            Plataforma dedicada a la formación tecnológica mediante <br>
            cursos, mentorías y proyectos educativos para estudiantes.
        </p>
    </div>

    <div class="footerItem">
        <div class="footerIcono">✉</div>
        <div>
            <h3>Contacto:</h3>
            <p>aprendomo.contacto@gmail.com</p>
        </div>
    </div>

    <div class="footerItem">
        <div class="footerIcono">☎</div>
        <div>
            <h3>Teléfono:</h3>
            <p>+598 97 213 298</p>
        </div>
    </div>

    <div class="footerItem">
        <div class="footerIcono">●</div>
        <div>
            <h3>Ubicación:</h3>
            <p>Florencio Sánchez 398, Salto,<br>Departamento de Salto, Uruguay.</p>
        </div>
    </div>

    <a href="<?php echo $phpBase; ?>manual.php" id="manualUsuario">
        <div class="footerItem">
            <div class="footerIcono">▣</div>
            <div>
                <h3>Manual de usuario:</h3>
                <p>Aprende a utilizar Aprendomo</p>
            </div>    
        </div>
    </a>

    <div class="copyright">
        <p>© 2026 Aprendomo. Todos los derechos reservados.</p>
    </div>

</footer>

<!-- MINI CHAT: PREGUNTAS FRECUENTES -->
<div id="faq-widget">

    <!-- Botón flotante -->
    <button
        type="button"
        id="faq-toggle"
        aria-label="Abrir preguntas frecuentes"
        aria-expanded="false"
        aria-controls="faq-panel">

        <span class="faq-toggle-icon">?</span>
        <span class="faq-toggle-text">Preguntas frecuentes</span>

    </button>

    <!-- Ventana del mini chat -->
    <section id="faq-panel" aria-label="Centro de ayuda" hidden>

        <!-- Encabezado -->
        <div class="faq-header">

            <div class="faq-header-info">
                <div class="faq-avatar">A</div>

                <div>
                    <h2>Centro de ayuda</h2>
                    <p>Preguntas frecuentes de Aprendomo</p>
                </div>
            </div>

            <button type="button" id="faq-close" aria-label="Cerrar preguntas frecuentes"> &times; </button>

        </div>

        <!-- Contenido dinámico -->
        <div class="faq-body">

            <div class="faq-welcome" id="faq-welcome">
                <p class="faq-message">
                    ¡Hola! 👋 Selecciona una pregunta para conocer la respuesta.
                </p>
            </div>

            <!-- Lista de preguntas -->
            <div id="faq-questions">

                <p class="faq-section-title">
                    Preguntas frecuentes
                </p>

                <button type="button" class="faq-question" data-faq="0">
                    ¿Cómo puedo crear una cuenta?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="1">
                    ¿Cómo me inscribo a un curso?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="2">
                    ¿Dónde encuentro mis cursos?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="3">
                    ¿Cómo puedo acceder a los materiales?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="4">
                    ¿Cómo contacto al docente?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="5">
                    ¿Qué hago si olvidé mi contraseña?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="6">
                    ¿Cómo puedo editar mi perfil?
                    <span>›</span>
                </button>

                <button type="button" class="faq-question" data-faq="7">
                    ¿Cómo puedo solicitar ayuda?
                    <span>›</span>
                </button>

            </div>

            <!-- Respuesta seleccionada -->
            <div id="faq-answer-view" hidden>

                <button type="button" id="faq-back">
                    <span>←</span> Volver a las preguntas
                </button>

                <div class="faq-answer-card">
                    <p class="faq-answer-label">Tu pregunta</p>
                    <h3 id="faq-answer-question"></h3>

                    <div class="faq-answer-divider"></div>

                    <p class="faq-answer-label">Respuesta</p>
                    <p id="faq-answer-text"></p>
                </div>

            </div>

        </div>

        <!-- Pie del chat -->
        <div class="faq-footer">
            <span>Aprendomo · Centro de ayuda</span>
        </div>

    </section>

</div>

