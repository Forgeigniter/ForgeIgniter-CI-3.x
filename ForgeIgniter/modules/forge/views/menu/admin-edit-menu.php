<h1>Edit Menu</h1>
    <form action="<?php echo site_url('forge/menus/edit_menu/' . $menu['menu_id']); ?>" method="post">
        <label for="menu_name">Menu Name:</label>
        <input type="text" name="menu_name" id="menu_name" value="<?php echo $menu['menu_name']; ?>" required>
        <input type="submit" value="Update Menu">
        <input type="button" value="Cancel" id="cancel" onclick="location.href='<?php echo site_url('admin/menus'); ?>'">
    </form>