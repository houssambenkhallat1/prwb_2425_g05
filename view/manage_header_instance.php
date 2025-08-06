<div class="d-flex align-items-center p-2 bg-gradient ">
    <a href="form/list<?= $search_form ?>" class="text-white text-decoration-none me-3">
        <i class="bi bi-arrow-left text-primary fs-4"></i>
    </a>
    <div class="ms-auto">
        <a href="instance/get_instances/<?= $form->get_id()?><?= $search_form ?>" class="text-white text-decoration-none me-3">
            <i class="bi bi-clock-history text-primary fs-4"></i>
        </a>
        <a href="instance/clear_all_instances/<?= $form->get_id()?>" class="text-white text-decoration-none me-3">
            <i class="bi bi-arrow-repeat text-primary fs-3 "></i>
        </a>
        <form method="POST" action="form/delete_confirmation/<?= $search_form ?>" class="d-inline">
            <input type="hidden" name="form_id" value="<?= $form->get_id()?>">
            <button type="submit"  class="btn btn-link p-0 border-0 bg-transparent">
                <i class="bi bi-trash text-primary fs-4"></i>
            </button>
        </form>
<!--        <a href="form/delete_confirmation/--><?php //= $form->get_id()?><!--"-->
<!--           class="btn btn-link p-0 border-0 bg-transparent">-->
<!--            <i class="bi bi-trash text-primary fs-4"></i></a>-->

        <a href="#" class="text-white text-decoration-none me-3">
            <i class="bi bi-share text-primary fs-4 "></i>
        </a>
        <a href="instance/analyze/<?= $form->get_id()?>/<?= $search_form ?>" class="text-white text-decoration-none me-3">
            <i class="bi bi-graph-up-arrow text-primary fs-4 "></i>
        </a>
    </div>
</div>