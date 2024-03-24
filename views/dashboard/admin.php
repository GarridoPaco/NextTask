<?php include_once __DIR__ . '/header-admin.php' ?>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>
<table id="usersList">
    <thead>
        <th>Id</th>
        <th>Foto</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Email</th>
        <th>Verificada</th>
        <th>Eliminar usuario</th>
    </thead>
    <tbody>
        <?php foreach ($users as $user) : ?>
            <tr class="userInfo">
                <td><?php echo ($user->id . " "); ?></td>
                <td><img src="<?php echo ("build/img/profile/" . $user->image . ".jpg"); ?>" alt="Imagen de perfil"></td>
                <td><?php echo ($user->name . " "); ?></td>
                <td><?php echo ($user->last_name . " "); ?></td>
                <td><?php echo ($user->email . " "); ?></td>
                <td><?php echo (($user->verified == 1) ? "Cuenta verificada" : "Cuenta no verificada" . " "); ?></td>
                <td>
                    <div class="adminAction">
                        <form action="/admin" method="post">
                            <input class="boton" type="submit" value="Eliminar usuario" />
                            <input type="hidden" name="user_id" value="<?php echo ($user->id); ?>">
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include_once __DIR__ . '/footer-dashboard.php' ?>
<?php $script = "<script src='build/js/utils.js'></script>"; ?>