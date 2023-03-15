<h1>Add Menu Item</h1>
<form action="<?php echo site_url('admin/menus/add-menu-item'); ?>" method="post">
    <label for="link_name">Link Name:</label>
    <input type="text" name="link_name" id="link_name" required>

    <label for="link_uri">Link URI:</label>
    <input type="text" name="link_uri" id="link_uri" required>

    <label for="menu_id">Menu ID:</label>
    <input type="number" name="menu_id" id="menu_id" required>

    <input type="submit" value="Add Menu Item">
    <input type="button" value="Cancel" id="cancel" onclick="location.href='<?php echo site_url('admin/menus'); ?>'">
</form>
