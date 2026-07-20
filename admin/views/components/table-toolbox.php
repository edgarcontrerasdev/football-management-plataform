<div class="wrap-toolbar">

    <form method="GET" class="toolbar-form" id="toolbarForm">

        <input type="hidden" name="page" value="<?= $_GET['page'] ?? '' ?>">

        <div class="toolbar-left">

        <?php if(!empty($toolbox['create'])): ?>

        <button type="button" class="btn-add" onclick="location.href='<?= $toolbox['create'] ?>'">
        <i class="fas fa-plus"></i>
        </button>

        <?php endif; ?>

        <?php if(!empty($toolbox['filters'])): ?>

        <?php foreach($toolbox['filters'] as $filter): ?>

        <?php if($filter['type']=="select"): ?>

        <?php
        $currentValue = $_GET[$filter['name']] ?? $filter['default'] ?? '';
        ?>

        <select name="<?= $filter['name'] ?>" class="select-filter">

            <?php if(isset($filter['label'])): ?>
                <option value="" disabled <?= $currentValue === '' ? 'selected' : '' ?>>
                    <?= $filter['label'] ?>
                </option>
            <?php endif; ?>

            <?php foreach($filter['options'] as $key=>$value): ?>

                <option value="<?= $key ?>"
                    <?= ($currentValue == $key) ? 'selected' : '' ?>>

                    <?= $value ?>

                </option>

            <?php endforeach; ?>

        </select>

        <?php endif; ?>

        <?php if($filter['type']=="search"): ?>

        <div class="search-box">

            <i class="fas fa-search"></i>

            <input type="text" 
                name="<?= $filter['name'] ?>"
                placeholder="<?= $filter['placeholder'] ?>"
                value="<?= $_GET[$filter['name']] ?? '' ?>">

        </div>

        <?php endif; ?>

        <?php endforeach; ?>

        <?php endif; ?>

        <div class="toolbar-actions">

            <button type="submit" class="btn-toolbar btn-search">
                Buscar
            </button>

            <a href="?page=<?= $_GET['page'] ?? '' ?>" class="btn-toolbar btn-clear">
                Limpiar
            </a>

        </div>

        </div>

    </form>

</div>