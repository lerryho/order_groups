<?php
require_once("order.php");

$search = isset($_GET["search"]) ? $_GET["search"] : "";
$filterBy = isset($_GET["filterBy"]) ? $_GET["filterBy"] : "all";
$filterValue = isset($_GET["filterValue"]) ? $_GET["filterValue"] : "";
$orderBy = isset($_GET["orderBy"]) ? $_GET["orderBy"] : "order_date";
$orderDirection = isset($_GET["orderDirection"]) ? $_GET["orderDirection"] : "asc";


$filterClause = "";
if ($filterBy != "all" && $filterValue != "") {
    if ($filterBy == "order_date" || $filterBy == "cancel_date") {
        $filterClause = "AND DATE(`$filterBy`) = '$filterValue'";
    } else {
        $filterClause = "AND `$filterBy` LIKE '%$filterValue%'";
    }
}


$sql = "SELECT * FROM `order` WHERE 
        (order_id LIKE '%$search%' OR 
        user_id LIKE '%$search%' OR 
        `status` LIKE '%$search%' OR
        product_type LIKE '%$search%' OR
        order_date LIKE '%$search%' OR 
        cancel_date LIKE '%$search%') 
        $filterClause
        ORDER BY $orderBy $orderDirection";

$result = $conn->query($sql);
$userCount = $result->num_rows;

if (isset($_GET["reset"])) {
    $search = "";
    $filterValue = "";
    $sql = "SELECT * FROM `order`";
    $result = $conn->query($sql);
    $userCount = $result->num_rows;
}

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
                    <h1 class="mt-4">訂單</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.html">首頁</a></li>
                        <li class="breadcrumb-item active">訂單</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">
                            DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                            <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                            .
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            訂單列表
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div>
                                    <input type="search" class="col" placeholder="搜尋" aria-label="就是搜尋" aria-describedby="button-addon2" name="search" value="<?= htmlspecialchars($search) ?>">
                                </div>
                                <div>
                                    <select name="filterBy">
                                        <option value="all">---詳細篩選---</option>
                                        <option value="order_id" <?= ($filterBy == "order_id") ? "selected" : "" ?>>訂單ID</option>
                                        <option value="user_id" <?= ($filterBy == "user_id") ? "selected" : "" ?>>會員ID</option>
                                        <option value="product_type" <?= ($filterBy == "product_type") ? "selected" : "" ?>>訂單類型</option>
                                        <option value="status" <?= ($filterBy == "status") ? "selected" : "" ?>>訂單狀態</option>
                                        <option value="order_date" <?= ($filterBy == "order_date") ? "selected" : "" ?>>下單日期</option>
                                        <option value="cancel_date" <?= ($filterBy == "cancel_date") ? "selected" : "" ?>>取消日期</option>
                                    </select>
                                    <input type="text" name="filterValue" placeholder="搜尋" value="<?= htmlspecialchars($filterValue) ?>">
                                </div>
                                <div>
                                    <button type="submit" id="button-addon2" class="btn btn-primary">搜尋</button>
                                    <button type="submit" name="reset" class="btn btn-secondary">重置</button>
                                    <select class="" name="orderBy">
                                        <option value="order_id" <?= ($orderBy == "order_id") ? "selected" : "" ?>>訂單ID</option>
                                        <option value="user_id" <?= ($orderBy == "user_id") ? "selected" : "" ?>>會員ID</option>
                                        <option value="product_type" <?= ($orderBy == "product_type") ? "selected" : "" ?>>訂單類型</option>
                                        <option value="status" <?= ($orderBy == "status") ? "selected" : "" ?>>訂單狀態</option>
                                        <option value="order_date" <?= ($orderBy == "order_date") ? "selected" : "" ?>>下單日期</option>
                                        <option value="cancel_date" <?= ($orderBy == "cancel_date") ? "selected" : "" ?>>取消日期</option>
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
                                                訂單ID
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                會員ID
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                訂單類型
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                訂單狀態
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                下單日期
                                            </a>
                                        </th>
                                        <th data-sortable="true" style="width:15%;" style="width: 15.004840271055178%;">
                                            <a href="#">
                                                取消日期
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $rows = $result->fetch_all(MYSQLI_ASSOC);

                                    foreach ($rows as $user) :
                                    ?>
                                        <tr>
                                            <td><a href="http://localhost/order/order_detail.php?order_id=<?= $user["order_id"] ?>"><?= $user["order_id"] ?></a></td>
                                            <td><?= $user["user_id"] ?></td>
                                            <td><?= $user["product_type"] ?></td>
                                            <td><?= $user["status"] ?></td>
                                            <td><?= $user["order_date"] ?></td>
                                            <td><?= $user["cancel_date"] ?></td>
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