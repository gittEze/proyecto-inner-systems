document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('faq-toggle');
    const panel = document.getElementById('faq-panel');
    const close = document.getElementById('faq-close');

    const welcome = document.getElementById('faq-welcome');
    const questionsContainer = document.getElementById('faq-questions');
    const answerView = document.getElementById('faq-answer-view');

    const answerQuestion = document.getElementById('faq-answer-question');
    const answerText = document.getElementById('faq-answer-text');
    const back = document.getElementById('faq-back');

    // Comprobar que el mini chat exista en esta página.
    if (
        !toggle || !panel || !close ||
        !welcome || !questionsContainer || !answerView ||
        !answerQuestion || !answerText || !back
    ) {
        return;
    }

    // Preguntas y respuestas predeterminadas.
    const preguntasFrecuentes = [
        {
            pregunta: '¿Cómo puedo crear una cuenta?',
            respuesta:
                'Para crear una cuenta, entra a la opción de registro de Aprendomo y completa los datos solicitados. Cuando termines, sigue las indicaciones de la plataforma para acceder con tu cuenta.'
        },
        {
            pregunta: '¿Cómo me inscribo a un curso?',
            respuesta:
                'Entra en la sección Cursos, busca el curso que te interesa y abre su página para consultar la información disponible. Si el curso permite la inscripción desde la plataforma, sigue las opciones que aparecen allí.'
        },
        {
            pregunta: '¿Dónde encuentro mis cursos?',
            respuesta:
                'Si ya estás inscrito en cursos, entra en la opción Mis cursos del menú de usuario para consultar los cursos asociados a tu cuenta.'
        },
        {
            pregunta: '¿Cómo puedo acceder a los materiales?',
            respuesta:
                'Abre el curso que estás realizando y busca la sección de materiales o recursos. Allí podrás consultar los contenidos que el docente haya publicado para el curso.'
        },
        {
            pregunta: '¿Cómo contacto al docente?',
            respuesta:
                'Entra al curso y consulta el apartado de contacto del docente. Allí podrás ver el nombre del docente creador, su teléfono y su correo electrónico, si esos datos están disponibles.'
        },
        {
            pregunta: '¿Qué hago si olvidé mi contraseña?',
            respuesta:
                'En la página de inicio de sesión, busca la opción para recuperar la contraseña. Sigue las instrucciones que aparecen en pantalla para continuar con el proceso.'
        },
        {
            pregunta: '¿Cómo puedo editar mi perfil?',
            respuesta:
                'Inicia sesión, abre el menú de usuario y entra en Mi perfil. Desde esa sección podrás consultar y modificar los datos que la plataforma permita editar.'
        },
        {
            pregunta: '¿Cómo puedo solicitar ayuda?',
            respuesta:
                'Si tienes una duda sobre un curso, puedes comunicarte con su docente mediante los datos de contacto disponibles dentro del curso. Para otras consultas, puedes utilizar el correo de contacto de Aprendomo que aparece en el pie de página.'
        }
    ];

    // Abrir o cerrar el panel.
    function abrirChat() {
        panel.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
    }

    function cerrarChat() {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
    }

    // Volver a la lista completa de preguntas.
    function mostrarPreguntas() {
        welcome.hidden = false;
        questionsContainer.hidden = false;
        answerView.hidden = true;
    }

    // Mostrar únicamente la pregunta elegida y su respuesta.
    function mostrarRespuesta(indice) {
        const seleccionada = preguntasFrecuentes[indice];

        if (!seleccionada) {
            return;
        }

        answerQuestion.textContent = seleccionada.pregunta;
        answerText.textContent = seleccionada.respuesta;

        welcome.hidden = true;
        questionsContainer.hidden = true;
        answerView.hidden = false;

        // Llevar la vista al principio del panel.
        document.querySelector('#faq-panel .faq-body').scrollTop = 0;
    }

    toggle.addEventListener('click', function () {
        if (panel.hidden) {
            abrirChat();
        } else {
            cerrarChat();
        }
    });

    close.addEventListener('click', cerrarChat);

    // Detectar qué pregunta se seleccionó.
    questionsContainer.addEventListener('click', function (event) {
        const boton = event.target.closest('[data-faq]');

        if (!boton) {
            return;
        }

        const indice = Number(boton.dataset.faq);
        mostrarRespuesta(indice);
    });

    // Regresar a las preguntas disponibles.
    back.addEventListener('click', function () {
        mostrarPreguntas();
    });

    // Permitir cerrar el chat con la tecla Escape.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !panel.hidden) {
            cerrarChat();
        }
    });

});