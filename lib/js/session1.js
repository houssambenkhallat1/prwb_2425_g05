$(document).ready(function() {
    // pour le cas d'un btn guest user, event du click sur btn guest user
    $(document).on('click', '.btn-guest-user', function () {
        const instanceId = $(this).data('instance-id');
        const button = $(this);

        $.ajax({
            url: 'session1/move_to_guest',
            method: 'POST',
            data: {
                instance_id: instanceId
            },
            success: function (data) {
                if (data.success) {
                    // Désactive le bouton cliqué
                    button.prop('disabled', true);

                    // Active le bouton "logged user" dans le même bloc
                    button.closest('.mb-3').find('.btn-logged-user').prop('disabled', false);
                    $('#' + data.id).html('Instance ' + data.id + ' by ' + data.name);                }
            },

            error: function (xhr, status, error) {
                console.error('Error:', xhr.responseJSON || error);
            }
        });
    });


    // pour le cas d'un btn logged user, event du click sur btn logged user
    $(document).on('click', '.btn-logged-user', function () {
        const instanceId = $(this).data('instance-id');
        const button = $(this);

        $.ajax({
            url: 'session1/move_to_logged_user',
            method: 'POST',
            data: {
                instance_id: instanceId
            },
            success: function (data) {
                // Désactive le bouton cliqué
                button.prop('disabled', true);

                // Active le bouton "guest user" dans le même bloc
                button.closest('.mb-3').find('.btn-guest-user').prop('disabled', false);
                $('#' + data.id).html('Instance ' + data.id + ' by ' + data.name);
            },
            error: function (xhr, status, error) {
                console.error('Error:', xhr.responseJSON || error);
            }
        });
    });
});