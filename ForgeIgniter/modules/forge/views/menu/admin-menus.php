<?php if ($this->session->flashdata('message')): ?>
    <div class="alert alert-success">
        <?php echo $this->session->flashdata('message'); ?>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger">
        <?php echo $this->session->flashdata('error'); ?>
    </div>
<?php endif; ?>

<h1>Admin Menus</h1>
<a href="<?php echo site_url('admin/menus/add-menu'); ?>">Add New Menu</a>
<a href="<?php echo site_url('admin/menus/add-menu-item'); ?>">Add New Menu Item</a>
<ul>
    <?php foreach ($menus as $menu): ?>
        <li>
            <?php echo $menu['menu_name']; ?>
            <a href="<?php echo site_url('admin/menus/edit-menu/' . $menu['menu_id']); ?>">Edit</a>
            <a href="<?php echo site_url('admin/menus/delete-menu/' . $menu['menu_id']); ?>" onclick="return confirm('Are you sure you want to delete this menu?')">Delete</a>
            <ul>
                <?php foreach ($menu_items[$menu['menu_id']] as $item): ?>
                    <li><?php echo $item['link_name']; ?> - <?php echo $item['link_uri']; ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>





