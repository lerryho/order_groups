<?php
require_once("order.php");

$search = isset($_GET["search"]) ? $_GET["search"] : "";
$filterBy = isset($_GET["filterBy"]) ? $_GET["filterBy"] : "all";
$filterValue = isset($_GET["filterValue"]) ? $_GET["filterValue"] : "";
$orderBy = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "name";
$orderDirection = isset($_GET["orderDirection"]) ? $_GET["orderDirection"] : "asc";

$filterClause = "";
if ($filterBy != "all" && $filterValue != "") {
    if ($filterBy == "name" || $filterBy == "cancel_date") {
        $filterClause = "AND DATE(`$filterBy`) = '$filterValue'";
    } else {
        $filterClause = "AND `$filterBy` LIKE '%$filterValue%'";
    }
}
$sql = "SELECT `product`.*, `product_category`.*, `product_category_groups`.*
        FROM `product`
        JOIN `product_category` ON `product`.`product_category_id` = `product_category`.`product_category_id`
        LEFT JOIN `product_category_groups` ON `product`.`group_id` = `product_category_groups`.`group_id`
        WHERE (`product`.`name` LIKE '%$search%' OR 
               `product`.`brand` LIKE '%$search%' OR 
               `product`.`price` LIKE '%$search%' OR 
               `product`.`product_category_id` LIKE '%$search%') 
               $filterClause
        ORDER BY $orderBy $orderDirection";



$result = $conn->query($sql);
$userCount = $result->num_rows;
if (isset($_GET["reset"])) {
    $search = "";
    $filterValue = "";
    $sql = "SELECT * FROM `product`";
    $result = $conn->query($sql);
    $userCount = $result->num_rows;
}
$sql = "SELECT `product`.*, `product_category`.*
        FROM `product`
        JOIN `product_category` ON `product`.`product_category_id` = `product_category`.`product_category_id`";

$query = "SELECT * FROM product_category_groups";
$groupStatement = $conn->query($query);
$groups = $groupStatement->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM product_category";
$categoryResult = $conn->query($query);
$categories = $categoryResult->fetch_all(MYSQLI_ASSOC);

$query = "SELECT * FROM product";
$productResult = $conn->query($query);
$products = $productResult->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create_group']) && isset($_POST['group_name'])) {
        $groupName = $_POST['group_name'];

        $existingGroups = array_column($groups, 'group_name');
        if (in_array($groupName, $existingGroups)) {
            echo "群組名稱已存在，請選擇其他名稱。";
        } else {

            $insertQuery = "INSERT INTO product_category_groups (group_name) VALUES (?)";
            $insertStatement = $conn->prepare($insertQuery);
            $insertStatement->bind_param('s', $groupName);
            $insertStatement->execute();
        }
    } elseif (isset($_POST['delete_group']) && isset($_POST['group_id'])) {
        $groupId = $_POST['group_id'];

        $deleteQuery = "DELETE FROM product_category_groups WHERE group_id = ?";
        $deleteStatement = $conn->prepare($deleteQuery);
        $deleteStatement->bind_param('i', $groupId);
        $deleteStatement->execute();
    } elseif (isset($_POST['rename_group']) && isset($_POST['group_id']) && isset($_POST['new_group_name'])) {
        $groupId = $_POST['group_id'];
        $newGroupName = $_POST['new_group_name'];

        $updateQuery = "UPDATE product_category_groups SET group_name = ? WHERE group_id = ?";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bind_param('si', $newGroupName, $groupId);
        $updateStatement->execute();
    } elseif (isset($_POST['add_to_group']) && isset($_POST['product_id']) && isset($_POST['group_id'])) {
        $productId = $_POST['product_id'];
        $groupId = $_POST['group_id'];

        $updateQuery = "UPDATE product SET group_id = ? WHERE product_id = ?";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bind_param('ii', $groupId, $productId);
        $updateStatement->execute();
    } elseif (isset($_POST['remove_from_group']) && isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];

        $updateQuery = "UPDATE product SET group_id = NULL WHERE product_id = ?";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bind_param('i', $productId);
        $updateStatement->execute();
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Tables - SB Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Start Bootstrap</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="index.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Layouts
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Pages
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Authentication
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="login.html">Login</a>
                                        <a class="nav-link" href="register.html">Register</a>
                                        <a class="nav-link" href="password.html">Forgot Password</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Error
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="401.html">401 Page</a>
                                        <a class="nav-link" href="404.html">404 Page</a>
                                        <a class="nav-link" href="500.html">500 Page</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading">Addons</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#groups" aria-expanded="false" aria-controls="groups">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            群組管理
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="groups" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="product_group.php">
                                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                    商品群組
                                </a>
                                <a class="nav-link" href="course_group.php">
                                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                    課程群組
                                </a>
                            </nav>
                        </div>
                        <a class="nav-link" href="tables.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            訂單
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Start Bootstrap
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">商品群組</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.html">首頁</a></li>
                        <li class="breadcrumb-item active">商品群組</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">已有群組名稱
                            <ul class="list-unstyled">
                                <?php foreach ($groups as $group) : ?>
                                    <li><?= $group['group_name'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div>群組操作
                        <form method="post">
                            <label for="group_name">建立群組：</label>
                            <input type="text" name="group_name" required>
                            <button type="submit" name="create_group">建立</button>
                        </form>

                        <form method="post">
                            <label for="group_id">刪除群組：</label>
                            <select name="group_id" required>
                                <option value="">選擇群組</option>
                                <?php foreach ($groups as $group) : ?>
                                    <option value="<?= $group['group_id'] ?>"><?= $group['group_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="delete_group">刪除</button>
                        </form>
                        <form method="post">
                            <label for="group_id">重新命名群組：</label>
                            <select name="group_id" required>
                                <option value="">選擇群組</option>
                                <?php foreach ($groups as $group) : ?>
                                    <option value="<?= $group['group_id'] ?>"><?= $group['group_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="new_group_name">新群組名稱：</label>
                            <input type="text" name="new_group_name" required>
                            <button type="submit" name="rename_group">重新命名</button>
                        </form>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            商品群組列表
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div>
                                    <input type="search" class="col" placeholder="搜尋" aria-label="就是搜尋" aria-describedby="button-addon2" name="search" value="<?= htmlspecialchars($search) ?>">
                                </div>
                                <div>
                                    <select name="filterBy">
                                        <option value="all">---詳細篩選---</option>
                                        <option value="name" <?= ($filterBy == "name") ? "selected" : "" ?>>名稱</option>
                                        <option value="price" <?= ($filterBy == "price") ? "selected" : "" ?>>價錢</option>
                                        <option value="product_category_id" <?= ($filterBy == "product_category_id") ? "selected" : "" ?>>類型</option>
                                        <option value="brand" <?= ($filterBy == "brand") ? "selected" : "" ?>>廠商</option>
                                        <option value="group_name" <?= ($filterBy == "group_name") ? "selected" : "" ?>>群組</option>
                                    </select>
                                    <input type="text" name="filterValue" placeholder="搜尋" value="<?= htmlspecialchars($filterValue) ?>">
                                </div>
                                <div>
                                    <button type="submit" id="button-addon2" class="btn btn-primary">搜尋</button>
                                    <button type="submit" name="reset" class="btn btn-secondary">重置</button>
                                    <select class="" name="orderBy">
                                        <option value="name" <?= ($orderBy == "name") ? "selected" : "" ?>>名稱</option>
                                        <option value="price" <?= ($orderBy == "price") ? "selected" : "" ?>>價錢</option>
                                        <option value="product_category_id" <?= ($orderBy == "product_category_id") ? "selected" : "" ?>>類型</option>
                                        <option value="brand" <?= ($orderBy == "brand") ? "selected" : "" ?>>廠商</option>
                                        <option value="group_name" <?= ($orderBy == "group_name") ? "selected" : "" ?>>群組</option>
                                    </select>
                                    <select class="" name="orderDirection">
                                        <option value="asc" <?= ($orderDirection == "asc") ? "selected" : "" ?>>遞增</option>
                                        <option value="desc" <?= ($orderDirection == "desc") ? "selected" : "" ?>>遞減</option>
                                    </select>
                                </div>
                            </form>
                            <table class="datatable-table" id="order">
                                <thead>
                                    <tr>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                名稱
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                價錢
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                類型
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                廠商
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                群組
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                群組操作
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                                    foreach ($rows as $product) :
                                    ?>
                                        <tr>
                                            <td><?= $product["name"] ?></td>
                                            <td><?= $product["price"] ?></td>
                                            <td>
                                                <?php
                                                $category_id = $product["product_category_id"];
                                                $category_name = "";

                                                if ($category_id == 1) {
                                                    $category_name = "小提琴";
                                                } elseif ($category_id == 2) {
                                                    $category_name = "小提琴盒";
                                                } elseif ($category_id == 3) {
                                                    $category_name = "提琴弓";
                                                } elseif ($category_id == 4) {
                                                    $category_name = "松香";
                                                } elseif ($category_id == 5) {
                                                    $category_name = "譜架";
                                                }
                                                echo $category_name;
                                                ?>
                                            </td>
                                            <td><?= $product["brand"] ?></td>
                                            <td>
                                                <?php if ($product['group_id'] !== null) : ?>
                                                    <?php
                                                    $group_id = $product['group_id'];
                                                    $group_name = '';
                                                    foreach ($groups as $group) {
                                                        if ($group['group_id'] == $group_id) {
                                                            $group_name = $group['group_name'];
                                                            break;
                                                        }
                                                    }
                                                    echo $group_name;
                                                    ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="post">
                                                    <select name="group_id">
                                                        <option value="">選擇群組</option>
                                                        <?php foreach ($groups as $group) : ?>
                                                            <option value="<?= $group['group_id'] ?>"><?= $group['group_name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                    <button type="submit" name="add_to_group">加入群組</button>
                                                    <button type="submit" name="remove_from_group">移出群組</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="datatable-bottom">
                            <div class="datatable-info">
                                共 <?= $userCount ?> 筆資料
                            </div>

                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>