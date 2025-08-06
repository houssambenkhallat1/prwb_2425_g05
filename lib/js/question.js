document.addEventListener('DOMContentLoaded', function () {
    // Get form elements
    const form = document.querySelector('form');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const typeSelect = document.getElementById('type');
    const submitButton = document.getElementById('submitButton');
    const submitLabel = document.querySelector('label[for="submitButton"]');

    // Configuration values (will be fetched from PHP)
    let config = {
        title_min_length: 3,
        title_max_length: 255,
        description_max_length: 1000,
        description_min_length: 3
    };

    // Keep track of validation status
    const validationStatus = {
        title: false,
        type: false,
        description: true // Description can be empty
    };

    // Fetch configuration values via AJAX


    // Initialize validations
    function init() {


        // Set initial validation states
        if (titleInput.value.trim().length > 0) {
            validateTitle();
        }

        if (typeSelect.value) {
            validateType();
        } else {
            validationStatus.type = false;
        }

        // Add event listeners
        titleInput.addEventListener('input', debounce(validateTitle, 300));
        descriptionInput.addEventListener('input', debounce(validateDescription, 300));
        typeSelect.addEventListener('change', validateType);

        // Override form submission
        form.addEventListener('submit', handleSubmit);

        // Initial button state
        updateSubmitButtonState();
    }

    // Debounce function to prevent too many validation calls
    function debounce(func, delay) {
        let timeout;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Validate title
    function validateTitle() {
        const title = titleInput.value.trim();
        removeValidationFeedback(titleInput);

        // Check length
        if (title.length === 0) {
            showError(titleInput, 'Title is required');
            validationStatus.title = false;
            updateSubmitButtonState();
            return;
        }

        if (title.length < config.title_min_length) {
            showError(titleInput, `Title must be at least ${config.title_min_length} characters long`);
            validationStatus.title = false;
            updateSubmitButtonState();
            return;
        }

        if (title.length > config.title_max_length) {
            showError(titleInput, `Title must not exceed ${config.title_max_length} characters`);
            validationStatus.title = false;
            updateSubmitButtonState();
            return;
        }

        // Check uniqueness via AJAX
        const formId = document.querySelector('input[name="form_id"]')?.value ||
            document.querySelector('form').action.split('/').pop();
        const questionId = document.querySelector('input[name="id"]')?.value || null;
        // Créer un objet avec les données à envoyer
        const data = {
            title: title,
            form_id: formId
        };

        // Ajouter question_id seulement s'il existe
        if (questionId) {
            data.question_id = questionId;
        }

        // Construire l'URL correctement sans utiliser URLSearchParams

        fetch('question/check_title', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                title: title.trim(),
                form_id: formId,
                question_id: questionId || null
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.unique) {
                    showSuccess(titleInput);
                    validationStatus.title = true;
                } else {
                    showError(titleInput, 'This title already exists in this form');
                    validationStatus.title = false;
                }
                if (data.error) {
                    showError(titleInput, 'data error');
                    validationStatus.title = false;
                }
                updateSubmitButtonState();
            })
            .catch(error => {
                console.error('Error checking title uniqueness:', error);
                // Assume valid if cannot check
                showSuccess(titleInput);
                validationStatus.title = true;
                updateSubmitButtonState();
            });
    }

    // Validate description
    function validateDescription() {
        const description = descriptionInput.value.trim();
        removeValidationFeedback(descriptionInput);

        // Description can be empty
        if (description.length === 0) {
            showSuccess(descriptionInput);
            validationStatus.description = true;
            updateSubmitButtonState();
            return;
        }
        if (description.length < config.description_min_length) {
            showError(descriptionInput, `Description must be at least ${config.description_min_length} characters long`);
            validationStatus.description = false;
            updateSubmitButtonState();
            return;
        }

        if (description.length > config.description_max_length) {
            showError(descriptionInput, `Description must not exceed ${config.description_max_length} characters`);
            validationStatus.description = false;
        } else {
            showSuccess(descriptionInput);
            validationStatus.description = true;
        }

        updateSubmitButtonState();
    }

    // Validate type
    function validateType() {
        removeValidationFeedback(typeSelect);

        if (!typeSelect.value) {
            showError(typeSelect, 'Type is required');
            validationStatus.type = false;
        } else {
            showSuccess(typeSelect);
            validationStatus.type = true;
        }

        updateSubmitButtonState();
    }

    // Show error message for an input
    function showError(input, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');

        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.insertBefore(feedback, input.nextElementSibling);
        }

        feedback.textContent = message;
    }

    // Show success state for an input
    function showSuccess(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');

        // Remove any existing error message
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }

    // Remove validation feedback
    function removeValidationFeedback(input) {
        input.classList.remove('is-valid', 'is-invalid');

        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }

    // Update submit button state
    function updateSubmitButtonState() {
        const isValid = validationStatus.title && validationStatus.type && validationStatus.description;

        if (isValid) {
            submitLabel.classList.remove('disabled');
            submitLabel.style.opacity = '1';
            submitButton.disabled = false;
        } else {
            submitLabel.classList.add('disabled');
            submitLabel.style.opacity = '0.5';
            submitButton.disabled = true;
        }
    }

    // Handle form submission
    // Handle form submission
    function handleSubmit(event) {
        event.preventDefault();

        if (!validationStatus.title || !validationStatus.type || !validationStatus.description) {
            return false;
        }

        const formData = new FormData(form);
        const url = form.getAttribute('action');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to form management page
                    const segments = window.location.pathname.split('/');
                    // Vérifie qu’on est bien sur /question/add/{id}/[valeur_optionnelle]
                    let searchForm = '';
                    if (segments.length >= 5 && segments[2] === 'question' && segments[3] === 'add') {
                        searchForm = segments[5] ?? '';
                    }
                    window.location.href = `form/manage/${data.form_id}/${searchForm}`;
                } else if (data.errors) {
                    // Display server-side validation errors
                    for (const [field, message] of Object.entries(data.errors)) {
                        const input = document.getElementById(field);
                        if (input) {
                            showError(input, message);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting form:', error);
            });
    }

    // Start validation
    init();
});
