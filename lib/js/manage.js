$(document).ready(function() {
    let deleteQuestionId = null;
    let formId = null;

    // Gestionnaire d'ouverture de modale (version jQuery)
    $(document).on('click', '.delete-question', function() {
        deleteQuestionId = $(this).data('question-id');
        formId = $(this).data('form-id');
        $('#questionTitle').text($(this).data('question-title'));
        $('#deleteQuestionModal').modal('show');
    });

    // Gestionnaire de confirmation (version jQuery)
    $('#confirmDelete').on('click', function() {
        // Vérifier que webRoot est défini
        const baseUrl = (typeof webRoot !== 'undefined') ? webRoot : '';

        $.ajax({
            url: baseUrl + 'question/delete',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                question_id: deleteQuestionId,
                form_id: formId
            }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data.success) {
                    $(`[data-id="${deleteQuestionId}"]`).remove();
                    $('#deleteQuestionModal').modal('hide');
                    updateAllQuestionControls();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseJSON || error);
                alert('An error occurred: ' + (xhr.responseJSON?.error || error));
            }
        });
    });

    // Fonction pour mettre à jour l'état des boutons up/down
    function updateButtonStates(container, position, totalQuestions) {
        const upButton = container.find('.btn-up');
        const downButton = container.find('.btn-down');

        // Bouton Up
        const isFirst = position === 0;
        upButton.prop('disabled', isFirst);
        upButton.toggleClass('btn-outline-secondary', !isFirst);
        upButton.toggleClass('btn-outline-dark', isFirst);

        // Bouton Down
        const isLast = position === totalQuestions - 1;
        downButton.prop('disabled', isLast);
        downButton.toggleClass('btn-outline-secondary', !isLast);
        downButton.toggleClass('btn-outline-dark', isLast);
    }

    // Fonction pour mettre à jour tous les contrôles
    function updateAllQuestionControls() {
        $('.form-container').each(function(index) {
            updateButtonStates($(this), index, $('.form-container').length);
        });
    }

    // Met à jour l'état des boutons up/down pour un conteneur spécifique
    function updateQuestionControls(container) {
        const idx = container.index();
        const total = $('.form-container').length;
        updateButtonStates(container, idx, total);
    }

    // Mise à jour des boutons au chargement de la page
    updateAllQuestionControls();

    // Gestion du bouton "Up"
    $(document).on('click', '.btn-up', function() {
        const button = $(this);
        const questionDiv = button.closest('.form-container');
        const prevDiv = questionDiv.prev('.form-container');

        if (prevDiv.length === 0) return; // Déjà en première position

        $.ajax({
            url: 'question/up',
            method: 'POST',
            data: { question_id: button.val() },
            success: function() {
                // Échange visuel des questions
                questionDiv.insertBefore(prevDiv);

                // Mise à jour des boutons
                updateQuestionControls(questionDiv);
                updateQuestionControls(prevDiv);
            },
            error: function() {
                alert("Erreur lors du déplacement");
            }
        });
    });

    // Gestion du bouton "Down"
    $(document).on('click', '.btn-down', function() {
        const button = $(this);
        const questionDiv = button.closest('.form-container');
        const nextDiv = questionDiv.next('.form-container');

        if (nextDiv.length === 0) return; // Déjà en dernière position

        $.ajax({
            url: 'question/down',
            method: 'POST',
            data: { question_id: button.val() },
            success: function() {
                // Échange visuel des questions
                questionDiv.insertAfter(nextDiv);

                // Mise à jour des boutons
                updateQuestionControls(questionDiv);
                updateQuestionControls(nextDiv);
            },
            error: function() {
                alert("Erreur lors du déplacement");
            }
        });
    });

    // Configuration du drag & drop
    $(".questions-container").sortable({
        handle: ".handle",         // Élément utilisé pour saisir la question
        placeholder: "sortable-placeholder", // Classe pour l'emplacement temporaire
        axis: "y",                 // Limiter le déplacement vertical uniquement
        tolerance: "pointer",      // Méthode de calcul de la position
        cursor: "move",            // Curseur pendant le déplacement

        // Animation pendant le déplacement
        start: function(e, ui) {
            ui.item.addClass('sortable-dragging');
        },
        stop: function(e, ui) {
            ui.item.removeClass('sortable-dragging');
        },

        // Événement déclenché après le réordonnancement
        update: function(event, ui) {
            // Récupérer tous les IDs des questions dans leur ordre actuel
            const sortedIds = $(this).sortable("toArray", { attribute: "data-id" });

            // Envoyer l'ordre mis à jour au serveur via AJAX
            $.ajax({
                url: 'question/reorder',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ order: sortedIds }),
                success: function(response) {
                    // Mettre à jour les contrôles de toutes les questions
                    updateAllQuestionControls();
                },
                error: function(xhr) {
                    console.error("Erreur:", xhr.responseJSON?.error || "Erreur inconnue");
                    // Annuler le tri visuel en cas d'erreur
                    $(".questions-container").sortable("cancel");
                    alert("Une erreur est survenue lors du réordonnancement");
                }
            });
        }
    }).disableSelection();  // Empêcher la sélection de texte pendant le déplacement
});