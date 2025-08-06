<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/instance.css">
    <title>Edit instance</title>
</head>
<body>
    <div class="position-relative">
        <header class="form-container d-flex justify-content-between p-2 text-white">
            <!-- forms link -->
            <a href="form/list/<?= $search_form ?>"><span class="light-blue mt-1"><i class="bi bi-arrow-left"></i></span></a>

            <div id="btn-header-container" class="d-flex justify-content-end">
                <!-- CANCEL LINK -->
                <?php if(!$instance->is_completed()): ?>
                    <a id="btn-cancel" class="btn btn-dark" href="instance/cancel_submission_box/<?= $instance->get_id() ?>/<?= $search_form ?>"><i class="bi bi-x-circle"></i></a>
                <?php endif; ?>
                <!-- PREVIOUS QUESTION LINK -->
                <?php if($question->has_previous_question_idx()): ?>
                    <a class="btn btn-dark"
                       href="instance/update/<?= $instance->get_id() ?>/<?= $question->get_idx() - 1 ?>/<?= $search_form ?>">
                        <i class="bi bi-arrow-left-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
        </header>
        <main class="form-container p-2 text-white">
            <h3>Answer the form</h3>
            <div class="pl-4">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Title</td>
                            <td><?= $form->get_title() ?></td>
                        </tr>
                        <tr>
                            <td>Description </td>
                            <td><?= $form->get_description() ? $form->get_description() : "/" ?></td>
                        </tr>
                        <tr>
                            <td>Started</td>
                            <td><?= $instance->start($instance->get_started()) ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><i><?= $form->get_last_instance_status($user->get_id()) ?></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <h3>Question <?= $question->get_idx() ?> / <?= $count_questions ?></h3>
                <p class="mt-4 pl-4"><?= $question->get_title() ?> <?= $question->is_required() ? "<span class='text-danger'>(*)</span>" : ""?></p>
                <div>
                    <form method="post" action="instance/update/<?= $instance->get_id() ?>/<?= $question->get_idx() ?>/<?= $search_form ?>">
                        <div class="form-group">
                            <label class="font-italic" for="answer"><small><?= $question->get_description() ?></small></label>
                            <input class="form-control <?= $answer ? (empty($errors) ? 'is-valid' : 'is-invalid') : ''?>"
                                   id="answer"
                                   name="answer"
                                   value="<?= $answer ? $answer->get_value() : '' ?>">
                            <?php if($errors && !empty($errors)): ?>
                            <ul class="mt-2">
                                <?php foreach($errors as $error): ?>
                                    <li class="text-danger"><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                        <?php if($question->has_next_question_idx($count_questions)): ?>
                        <div>
                            <button type="submit" class="btn btn-dark next-button mt-2"><i class="bi bi-arrow-right-circle"></i></button>
                        </div>
                        <?php elseif(!$question->has_next_question_idx($count_questions)): ?>
                        <div>
                            <button class="btn btn-dark next-button mt-2"><i class="bi bi-floppy"></i></button>
                        </div>
                        <?php endif; ?>
                    </form>

                </div>
            </div>
        </main>
    </div>
</body>
