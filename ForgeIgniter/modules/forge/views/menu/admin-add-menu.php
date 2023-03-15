<h1>Add Menu</h1>
<form action="<?php echo site_url('admin/menus/add-menu'); ?>" method="post">
    <label for="menu_name">Menu Name:</label>
    <input type="text" name="menu_name" id="menu_name" required>
    <input type="submit" value="Create Menu">
    <input type="button" value="Cancel" id="cancel" onclick="location.href='<?php echo site_url('admin/menus'); ?>'">
</form>
