<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <title>Statistics Analysis</title>
</head>
<body>
<div class="container-sm">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="form/manage/<?= $form->get_id() ?>/<?= $search_form ?>" class="text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h2 class="ms-3 mb-0">Statistics of form "<?= $form->get_title() ?>"</h2>
            </div>
            <hr class="mb-4">

            <!-- Formulaire de sélection -->
            <form method="POST" action="instance/analyze/<?= $form->get_id() ?>/<?= $search_form ?>" id="analysisForm" class="mb-4">
                <input type="hidden" name="form_id" value="<?= $form->get_id() ?>">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <select class="form-select" name="question_id" id="questionSelect" required>
                            <option value="" disabled selected>-- Select a question --</option>
                            <?php foreach ($questions as $question): ?>
                                <option value="<?= $question->get_id() ?>"
                                    <?= isset($selected_question) && $selected_question->get_id() === $question->get_id() ? 'selected' : '' ?>>
                                    <?= $question->get_idx() ?>. <?= $question->get_title() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php
                    if(Configuration::get('disable_js')){
                        echo'<div class="col-md-4">
                                <button type="submit" class="btn btn-primary mb-3">
                                    <i class="bi bi-graph-up"></i> Show Statistics
                                </button>
                            </div>';
                    }
                    ?>
                        <!-- Statistics Results -->
            <?php if (isset($statistics) && isset($selected_question)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Results for: <?= $selected_question->get_title() ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Value</th>
                                        <th>Count</th>
                                        <th>Ratio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($statistics as $stat): ?>
                                        <tr>
                                            <td><?= $stat['value'] ?></td>
                                            <td><?= $stat['count'] ?></td>
                                            <td><?= $stat['ratio'] ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr class="table-active">
                                        <td><strong>Total</strong></td>
                                        <td><strong><?= $total_responses ?></strong></td>
                                        <td><strong>100%</strong></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>


                    <div class="col-md-4 d-none" id="chartTypeContainer">
                        <select class="form-select" id="chartType">
                            <option value="pie">Pie Chart</option>
                            <option value="doughnut">Doughnut</option>
                            <option value="bar">Bar Chart</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Graphique -->
            <div class="card mt-4 d-none" id="chartContainer">
                <div class="card-body">
                    <canvas id="analysisChart" style="max-height: 400px;"></canvas>
                </div>
            </div>

            <!-- Tableau (toujours présent mais caché) -->
            <div class="card mt-4 d-none" id="resultsTable">
                <div class="card-header">
                    <h5 class="card-title mb-0">Results</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Value</th>
                                <th>Count</th>
                                <th>Ratio</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                            <tr class="table-active">
                                <td><strong>Total</strong></td>
                                <td><strong id="totalResponses">0</strong></td>
                                <td><strong>100%</strong></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Messages d'erreur -->
            <div id="errorAlert" class="alert alert-danger mt-4 d-none"></div>
        </div>
    </div>
</div>

<script src="lib/js/analyze.js">
    <?php if(isset($selected_question)): ?>
    // Initialisation si question présélectionnée
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('chartTypeContainer').classList.remove('d-none');
        loadChartData();
    });
    <?php endif; ?>
</script>

</body>
</html>