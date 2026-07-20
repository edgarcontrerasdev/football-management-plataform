
<!-- components/table.php -->

<div class="table-container">

    <table class="table-custom">

        <thead>
            
            <tr>
            <?php foreach($columns as $c): ?>
                <th><?= $c ?></th>
            <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
        <?php if(empty($rows)): ?>
            <tr>
                <td colspan="<?= count($columns) ?>" class="table-empty">
                    No hay registros disponibles
                </td>
            </tr>
        <?php else: ?>

        <?php foreach($rows as $row): ?>
            <tr class="table-row">
                <?php foreach($row as $cell): ?>
                <td> <?= $cell ?> </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>