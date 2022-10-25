<?php include ext('layoutDash.menuDash') ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>DashboardKit Bootstrap 5 Admin Template</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />

    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.svg" type="image/x-icon">

    <?php foreach ($linksCss as $value) : ?>
        <link href="<?= $value ?>" rel="stylesheet" />
    <?php endforeach; ?>

    <style>
        .m-header a {
            text-decoration: none;
        }
    </style>
</head>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Mobile header ] start -->
    <div class="pc-mob-header pc-header">
        <div class="pcm-logo">
            <!-- <img src="assets/images/logo.svg" alt="" class="logo logo-lg"> -->
            <p class="logo logo-lg text-white fs-2 mt-2 mx-auto"> <i class="bi bi-truck"></i> <?= $title ?></p>
        </div>
        <div class="pcm-toolbar">
            <a href="#!" class="pc-head-link" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
                <!-- <i data-feather="menu"></i> -->
            </a>

            <a href="#!" class="pc-head-link" id="header-collapse">
                <i class="text-white bi bi-three-dots-vertical"></i>
            </a>
        </div>
    </div>
    <!-- [ Mobile header ] End -->

    <!-- [ navigation menu ] start -->
    <nav class="pc-sidebar ">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="<?= $mainLink ?>" class="b-brand text-white fs-3 mt-2 mx-auto">
                    <!-- ========   change your logo hear   ============ -->
                    <i class="bi bi-truck"></i>
                    <span class="logo logo-lg"><?= $title ?></span>
                    <!-- <span class="logo logo-sm"><?= $titleShort ?></span> -->
                    <!-- <img src="assets/images/logo.svg" alt="" class="logo logo-lg">
					<img src="assets/images/logo-sm.svg" alt="" class="logo logo-sm"> -->
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">

                    <?php foreach ($linksSidebar as $key => $value) : ?>
                        <!-- UNA SOLA LINEA - TITULO-->
                        <?php if (isset($value['header'])) : ?>
                            <li class="pc-item pc-caption">
                                <label><?= $value['header']; ?></label>
                            </li>
                        <?php endif; ?>
                        <!-- UNA SOLA LINEA - LINK-->
                        <?php if (isset($value['mode']) && $value['mode'] == 'menu') : ?>
                            <li class="pc-item">
                                <a href="<?= $value['url']; ?>" class="pc-link ">
                                    <span class="pc-micon">
                                        <i class="<?= $value['icon']; ?>"></i>
                                    </span>
                                    <span class="pc-mtext"> <?= $value['text']; ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <!-- SUBMENUS - LINK-->
                        <?php if (isset($value['mode']) && $value['mode'] == 'submenu') : ?>

                            <li class="pc-item pc-hasmenu">
                                <a href="#!" class="pc-link ">
                                    <span class="pc-micon">
                                        <i class="<?= $value['icon']; ?>"></i>
                                    </span>
                                    <span class="pc-mtext"><?= $value['text']; ?></span>
                                    <span class="pc-arrow"><i class="bi bi-chevron-compact-right"></i></span>
                                </a>
                                <ul class="pc-submenu">
                                    <?php foreach ($value['submenu'] as $subValue) : ?>
                                        <li class="pc-item">
                                            <a class="pc-link" href="<?= $subValue['url']; ?>">
                                                <?= $subValue['text']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <header class="pc-header ">
        <div class="header-wrapper">
            <div class="m-header d-flex align-items-center">
                <a class="pc-head-link me-0" href="#" id="vertical-nav-toggle">
                    <i class="bi bi-list"></i>
                </a>
            </div>

            <div class="ms-auto">
                <ul class="list-unstyled">
                    <!-- <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="bi bi-search"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown drp-search">
                            <form class="px-3">
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <i data-feather="search"></i>
                                    <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . .">
                                </div>
                            </form>
                        </div>
                    </li>
                    <li class="dropdown pc-h-item pc-cart-menu">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="bi bi-bag-check"></i>
                            <span class="badge bg-success pc-h-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown drp-cart">
                            <div class="cart-head">
                                <h4 class="mb-0">3 Items</h4>
                                <a href="#!" class="text-danger float-end"><u>Remove All</u></a>
                                <p class="mb-0">in your cart</p>
                            </div>
                            <div class="cart-item">
                                <img src="assets/images/product/prod-1.jpg" alt="Product-img" title="Product-img" class="rounded me-3 border">
                                <div class="cart-desc">
                                    <a href="#!" class="text-body">
                                        <h6 class="d-inline-block mb-0">Villus Snow Ankle b...</h6>
                                    </a>
                                    <p class="mb-0">Army green,7</p>
                                    <span><span class="text-primary">$ 148.66</span> for <span class="text-primary">9</span> </span>
                                    <a href="#!" class="text-danger float-end"><i data-feather="trash-2"></i></a>
                                </div>
                            </div>
                            <div class="cart-item">
                                <img src="assets/images/product/prod-2.jpg" alt="Product-img" title="Product-img" class="rounded me-3 border">
                                <div class="cart-desc">
                                    <a href="#!" class="text-body">
                                        <h6 class="d-inline-block mb-0">Bullets Wireless Black</h6>
                                    </a>
                                    <p class="mb-0">Dark Black</p>
                                    <span><span class="text-primary">$ 299</span> for <span class="text-primary">5</span> </span>
                                    <a href="#!" class="text-danger float-end"><i data-feather="trash-2"></i></a>
                                </div>
                            </div>
                            <div class="cart-item">
                                <img src="assets/images/product/prod-3.jpg" alt="Product-img" title="Product-img" class="rounded me-3 border">
                                <div class="cart-desc">
                                    <a href="#!" class="text-body">
                                        <h6 class="d-inline-block mb-0">Parkas Thick Jackets</h6>
                                    </a>
                                    <p class="mb-0">Am Wine Red</p>
                                    <span><span class="text-primary">$ 599</span> for <span class="text-primary">3</span> </span>
                                    <a href="#!" class="text-danger float-end"><i data-feather="trash-2"></i></a>
                                </div>
                            </div>
                            <div class="table-responsive cart-item">
                                <table class="table table-borderless mb-0 table-xs">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h6 class="m-0">Sub Total:</h6>
                                            </td>
                                            <td class="text-right">
                                                $1070
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h6 class="m-0">Shipping:</h6>
                                            </td>
                                            <td class="text-right">
                                                FREE
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h5 class="m-0">Total:</h5>
                                            </td>
                                            <td class="text-right">
                                                <h5 class="m-0 text-primary font-weight-bold">$1070</h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <button class="btn btn-primary btn-sm btn-block mt-2">Check out</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </li>
                    <li class="pc-h-item">
                        <a class="pc-head-link me-0" href="#" data-bs-toggle="modal" data-bs-target="#notification-modal">
                            <i class="bi bi-bell"></i>
                            <span class="bg-danger pc-h-badge dots"><span class="sr-only"></span></span>
                        </a>
                    </li> -->
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="bi bi-person-circle fs-2"></i>
                            <!-- <img src="assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar"> -->
                            <span>
                                <span class="user-name"><?= $userName ?></span>
                                <span class="user-desc">Administrator</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <div class=" dropdown-header">
                                <h5 class="text-overflow m-0"><span class="badge bg-light-success">Pro</span></h5>
                            </div>

                            <?php foreach ($menuSession as $ms) : ?>
                                <a href="<?= $ms['url'] ?>" class="dropdown-item">
                                    <i class="<?= $ms['icon'] ?>"></i>
                                    <span><?= $ms['text'] ?></span>
                                </a>
                            <?php endforeach; ?>

                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </header>

    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">