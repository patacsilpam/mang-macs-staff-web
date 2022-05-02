<?php require 'public/staff-products.php'; ?>
<!DOCTYPE html>
<html lang="en-us">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Products" content="Mang Macs-Products">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/jpeg" href="assets/images/mang-macs-logo.jpg" sizes="70x70">
    <link rel="stylesheet" href="assets/css/main.css" type="text/css">
    <title>Products</title>
</head>

<body>
    <div class="grid-container">
        <!--Navigation-->
        <header class="nav-container">
            <h3>Products</h3>
            <ul class="nav-list">
                <?php include 'assets/template/navbar.php'?>
            </ul>
        </header>
        <!--Sales' Categories-->
        <main class="main-container">
            <section>
                <article>
                    <div class="table-responsive table-container">
                        <div class="add-product">
                            <button title="Add Product" type="button" class="btn btn-primary btn-add"
                                data-toggle="modal" data-target="#addProducts">Add &nbsp;<i
                                    class="fas fa-plus"></i></button>
                            <?php include 'assets/template/products.php'?>
                            <br><br>
                        </div>
                        <div>
                            <?php
                            //message box for registration
                            if (isset($_GET['insert-successfully'])) {
                            ?>
                            <small style="width:30%" class="alert alert-success msg-Success">Product successfully
                                inserted.</small>
                            <?php
                            }
                            if (isset($_GET['update-successfully'])) {
                            ?>
                            <small style="width:30%" class="alert alert-success msg-Success">Product successfully
                                updated.</small>
                            <?php
                            }
                            ?>
                        </div>
                        <table id="example" class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Product Category</th>
                                    <th scope="col">Variation</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $selectProduct = "SELECT * FROM tblproducts";
                                $displayProduct = $connect->query($selectProduct);
                                while ($fetch = $displayProduct->fetch_assoc()) {
                                    
                                ?>
                                <tr>
                                    <th scope="row"><?= $fetch['id'] ?></th>
                                    <td><?= $fetch['created_at'] ?></td>
                                    <td><img src="<?= $fetch['productImage']?>" alt="" width="50"></td>
                                    <td><?= $fetch['productName']?></td>
                                    <td><?= $fetch['productCategory'] ?></td>
                                    <td><?= $fetch['productVariation'] ?></td>
                                    <td><?= $fetch['price'] ?></td>
                                    <td><?= $fetch['status'] ?></td>
                                    <td style="display: flex;">
                                        <button title="Edit" type="button" class="btn btn-success" data-toggle="modal"
                                            data-target="#editProducts<?= $fetch['id']; ?>"><i
                                                class="fas fa-edit"></i></button>
                                        <?php include 'assets/template/products.php'?>&emsp;
                                        <button title="Delete" type="button" class="btn btn-danger" data-toggle="modal"
                                            data-target="#deleteProduct<?= $fetch['id']; ?>"><i
                                                class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </main>
        <!--Sidebar-->
        <?php include 'assets/template/sidebar.php'?>
    </div>
    <script src="assets/js/sidebar-menu-active.js"></script>
    <script src="assets/js/activePage.js"></script>
    <script src="assets/js/table.js"></script>
</body>

</html>