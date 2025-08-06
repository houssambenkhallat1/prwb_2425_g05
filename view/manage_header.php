<div class="d-flex align-items-center p-2 bg-gradient ">
    <a href="form/list<?= $search_form ?>" class="text-white text-decoration-none me-2">
        <i class="bi bi-arrow-left text-primary fs-4"></i>
    </a>
    <div class="ms-auto">
            <form action="question/add<?= $search_form ?>" method="POST" style="display: inline;">
            <input type="hidden" name="form_id" value="<?= $form->get_id() ?>">
            <button type="submit" class="btn p-0 border-0 bg-transparent text-white text-decoration-none me-2">
                <i class="bi bi-plus-square text-primary fs-4"></i>
            </button>
        </form>
        <a href="form/create_or_update/<?= $form->get_id()?><?= $search_form ?>" class="text-white text-decoration-none me-2">
            <i class="bi bi-pencil text-primary fs-4 "></i>
        </a>
        <form method="POST" action="form/delete_confirmation<?= $search_form ?>" class="d-inline">
            <input type="hidden" name="form_id" value="<?= $form->get_id()?>">
            <button type="submit"  class="btn btn-link p-0 border-0 bg-transparent">
                <i class="bi bi-trash text-primary fs-4"></i>
            </button>
        </form>
<!--        <a href="form/delete_confirmation/--><?php //=$form->get_id()?><!--" class="btn btn-link p-0 border-0 bg-transparent">-->
<!--            <i class="bi bi-trash text-primary fs-4"></i>-->
<!--        </a>-->
        <a href="#" class="text-white text-decoration-none me-2">
            <i class="bi bi-share text-primary fs-4 "></i>
        </a>
    </div>
</div>