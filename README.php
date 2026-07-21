 <?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
| Main router se $conn already mil raha ho to dobara include nahi hoga.
|--------------------------------------------------------------------------
*/

if (!isset($conn) || !($conn instanceof mysqli)) {
    require_once __DIR__ . '/connection/db_connect.php';
}


/*
|--------------------------------------------------------------------------
| SITE SETTINGS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT * FROM site_settings WHERE id = ? LIMIT 1"
);

$id = 1;

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$st = [];

if ($result && $result->num_rows > 0) {
    $st = $result->fetch_assoc();
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| CURRENT / LATEST ISSUE
|--------------------------------------------------------------------------
*/

$issue_sql = "
    SELECT *
    FROM issues
    ORDER BY id DESC
    LIMIT 1
";

$issue_res = mysqli_query($conn, $issue_sql);

$current_issue = $issue_res
    ? mysqli_fetch_assoc($issue_res)
    : null;


/*
|--------------------------------------------------------------------------
| ARTICLES OF CURRENT ISSUE
|--------------------------------------------------------------------------
*/

$art_result = null;

if ($current_issue) {

    $issue_id = (int) $current_issue['id'];

    $art_sql = "
        SELECT
            a.*,

            (
                SELECT GROUP_CONCAT(
                    c.full_name
                    SEPARATOR ', '
                )

                FROM article_contributors ac

                JOIN contributors c
                    ON ac.contributor_id = c.id

                WHERE ac.article_id = a.id

            ) AS authors

        FROM articles a

        WHERE a.issue_id = ?

        ORDER BY a.id ASC
    ";

    $articleStmt = $conn->prepare($art_sql);

    $articleStmt->bind_param(
        "i",
        $issue_id
    );

    $articleStmt->execute();

    $art_result =
        $articleStmt->get_result();
}

?>
<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Current Issue - Journal of Clinical & Industrial Pharmacy
    </title>

    <meta
        name="description"
        content="Current Issue of Journal of Clinical & Industrial Pharmacy"
    >

    <?php if (!empty($st['site_logo'])): ?>

        <link
            rel="icon"
            type="image/png"
            href="/research.ca/JCIP/media/<?= htmlspecialchars($st['site_logo']); ?>"
            ); ?>"
        >

    <?php endif; ?>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap"
        rel="stylesheet"
    >


<style>

:root {

    --primary-color:
        <?= htmlspecialchars(
            $st['primary_color'] ?? '#0d6efd'
        ); ?>;

    --dark-blue:
        <?= htmlspecialchars(
            $st['text_color'] ?? '#1f2937'
        ); ?>;

    --light-bg:
        <?= htmlspecialchars(
            $st['bg_color'] ?? '#f5f5f5'
        ); ?>;

    --transition:
        all 0.3s ease-in-out;
}


body {

    font-family:
        '<?= htmlspecialchars(
            $st['font_family'] ?? 'Poppins'
        ); ?>',
        sans-serif;
}


h1,
h2,
h3,
.fw-bold,
.nav-link {

    font-family:
        'Poppins',
        sans-serif;
}


.top-bar {

    background:
        var(--light-bg);

    font-size:
        13px;

    padding:
        8px 0;

    color:
        var(--dark-blue);
}


.navbar {

    transition:
        var(--transition);

    padding:
        15px 0;

    background:
        #fff;
}


.navbar-brand
.journal-title {

    color:
        var(--dark-blue);

    font-size:
        1.4rem;

    transition:
        var(--transition);
}


.nav-link {

    color:
        var(--dark-blue);

    font-weight:
        500;

    font-size:
        14px;

    position:
        relative;

    padding:
        10px 15px !important;

    transition:
        var(--transition);
}


.nav-link::before {

    content:
        "";

    position:
        absolute;

    width:
        0;

    height:
        2px;

    bottom:
        0;

    left:
        15px;

    background-color:
        var(--primary-color);

    transition:
        var(--transition);
}


.nav-link:hover::before,
.nav-link.active::before {

    width:
        calc(100% - 30px);
}


.nav-link:hover,
.navbar-nav
.nav-link.active {

    color:
        var(--primary-color)
        !important;
}


.dropdown-menu {

    border:
        none;

    border-radius:
        4px;

    box-shadow:
        0 5px 30px
        rgba(0,0,0,.1);

    border-top:
        3px solid
        var(--primary-color);

    padding:
        10px 0;
}


.dropdown-item {

    font-family:
        'Poppins',
        sans-serif;

    font-size:
        14px;

    font-weight:
        500;

    color:
        var(--dark-blue)
        !important;

    padding:
        10px 20px;

    transition:
        all .3s ease;
}


.dropdown-item:hover {

    color:
        var(--primary-color)
        !important;

    background-color:
        var(--light-bg);

    padding-left:
        25px;
}


.btn-theme-main {

    background-color:
        var(--dark-blue);

    transition:
        var(--transition);
}


.btn-theme-main:hover {

    background-color:
        var(--primary-color);

    transform:
        translateY(-2px);

    color:
        #fff;
}


.transition-hover {

    transition:
        var(--transition);
}


.transition-hover:hover {

    transform:
        scale(1.01);

    box-shadow:
        0 10px 20px
        rgba(0,0,0,.08)
        !important;
}

</style>

</head>


<body class="bg-light">


<?php
include('includes/header2.php');
?>


<div
    class="container-fluid py-5"
    style="
        background-color:#f9fbfb;
        margin-top:60px;
        margin-bottom:60px;
    "
>

<div class="row g-4 px-lg-4">


<?php
include('includes/sidebar.php');
?>


<div
    class="col-lg-9
    order-1
    order-lg-2"
>


<!-- BREADCRUMB -->

<nav
    aria-label="breadcrumb"
    class="mb-4"
>

<ol
    class="
        breadcrumb
        bg-white
        p-3
        rounded-4
        shadow-sm
        border-0
    "
>

<li class="breadcrumb-item">

<a
    href="home"
    class="
        text-decoration-none
        fw-medium
    "
    style="
        color:
        var(--dark-blue);
    "
>

Home

</a>

</li>


<li
    class="
        breadcrumb-item
        active
        fw-bold
        text-muted
    "
>

Current Issue

</li>

</ol>

</nav>


<div
    class="
        bg-white
        p-4
        p-lg-5
        rounded-4
        shadow-sm
        border-0
        min-vh-100
    "
>


<?php if ($current_issue): ?>


<!-- ISSUE HEADER -->

<div
    class="
        mb-5
        pb-3
        border-bottom
    "
>

<div
    class="
        row
        align-items-center
    "
>


<div
    class="
        col-md-3
        col-sm-4
        mb-3
        mb-md-0
        text-center
        text-sm-start
    "
>


<?php
if (
    !empty(
        $current_issue[
            'cover_image'
        ]
    )
):
?>


<img

src="/research.ca/JCIP/uploads/covers/<?= rawurlencode(
    basename($current_issue['cover_image'])
); ?>"

alt="Current Issue Cover"

class="
    img-fluid
    rounded
    shadow
"

style="
    width:140px;
    height:auto;
    object-fit:contain;
"

>


<?php else: ?>


<img

src="/research.ca/JCIP/uploads/default-cover.jpg"

alt="Default Cover"

class="
    img-fluid
    rounded
    shadow
    opacity-75
"

style="
    width:140px;
    height:auto;
    object-fit:contain;
"

>


<?php endif; ?>


</div>


<div
    class="
        col-md-9
        col-sm-8
    "
>


<h2

class="
    display-6
    fw-bold
    mb-1
"

style="
    color:
    var(--dark-blue);
"

>

Current Issue

</h2>


<p

class="
    text-muted
    text-uppercase
    fw-bold
    small
    mb-3
"

style="
    letter-spacing:
    2px;
"

>

<?= htmlspecialchars(
    $current_issue[
        'title'
    ]
); ?>

</p>


<div
    class="
        pt-2
        border-top
        border-light-subtle
    "
>


<p

class="
    text-secondary
    mb-0
"

style="
    font-size:
    .875rem;

    text-align:
    justify;

    line-height:
    1.7;
"

>

<?= htmlspecialchars(
    $current_issue[
        'description'
    ] ?? ''
); ?>

</p>


</div>

</div>

</div>

</div>


<!-- ISSUE INFO -->

<div class="row mb-5">

<div class="col-12">


<div

class="
    p-3
    rounded-4
    border
    d-flex
    justify-content-between
    align-items-center
    flex-wrap
    gap-3
"

style="
    background-color:
    var(--light-bg);
"

>


<div>

<span
    class="
        text-muted
        small
        text-uppercase
        fw-bold
    "
>

Published:

</span>


<span
    class="
        ms-2
        fw-bold
    "
    style="
        color:
        var(--dark-blue);
    "
>

<?php

if (
    !empty(
        $current_issue[
            'publish_date'
        ]
    )
) {

    echo date(
        'F d, Y',
        strtotime(
            $current_issue[
                'publish_date'
            ]
        )
    );

} else {

    echo 'N/A';
}

?>

</span>

</div>


<div class="d-flex gap-2">


<span
class="
    badge
    bg-white
    text-dark
    border
    rounded-pill
    px-3
    py-2
    shadow-sm
"
>

<i
class="
    fas
    fa-file-alt
    me-1
"
style="
    color:
    var(--primary-color);
"
></i>

<?= $art_result
    ? $art_result->num_rows
    : 0;
?>

Articles

</span>


<span
class="
    badge
    bg-white
    text-dark
    border
    rounded-pill
    px-3
    py-2
    shadow-sm
"
>

<i
class="
    fas
    fa-unlock
    me-1
    text-success
"
></i>

Open Access

</span>


</div>

</div>

</div>

</div>


<!-- ARTICLES -->

<h4
class="
    fw-bold
    mb-4
"
style="
    color:
    var(--dark-blue);
"
>

<i
class="
    fas
    fa-newspaper
    me-2
"
style="
    color:
    var(--primary-color);
"
></i>

Articles

</h4>


<?php if (
    $art_result
    &&
    $art_result->num_rows > 0
): ?>


<?php while (
    $article =
    $art_result->fetch_assoc()
): ?>


<div

class="
    card
    mb-4
    border-0
    rounded-4
    shadow-sm
    transition-hover
"

style="
    background:#fff;

    border:
    1px solid
    rgba(0,0,0,.05)
    !important;

    border-left:
    5px solid
    var(--dark-blue)
    !important;
"

>


<div class="card-body p-4">


<h5

class="
    fw-bold
    mb-3
"

style="
    color:
    var(--dark-blue);

    line-height:
    1.5;
"

>


<a

href="article?id=<?= (int)
    $article['id'];
?>"

class="
    text-decoration-none
"

style="
    color:inherit;
"

>

<?= htmlspecialchars(
    $article[
        'title'
    ]
); ?>

</a>

</h5>


<div
class="
    d-flex
    align-items-center
    flex-wrap
    gap-3
    mb-4
"
>


<p
class="
    text-dark
    small
    mb-0
"
>

<i
class="
    fas
    fa-user-edit
    me-2
    text-muted
"
></i>


<strong>

<?= !empty(
    $article[
        'authors'
    ]
)

? htmlspecialchars(
    $article[
        'authors'
    ]
)

: 'Author information unavailable';

?>

</strong>

</p>


<span
class="
    text-muted
    d-none
    d-md-block
"
>

|

</span>


<p
class="
    text-dark
    small
    mb-0
"
>

<i
class="
    fas
    fa-pager
    me-2
    text-muted
"
></i>

Pages:

<?= htmlspecialchars(
    $article[
        'page_no'
    ] ?: 'N/A'
); ?>

</p>


</div>


<div class="d-flex gap-2">


<!-- PDF -->

<?php if (
    !empty(
        $article[
            'pdf_file'
        ]
    )
): ?>


<a

href="/research.ca/JCIP/uploads/articles/<?= rawurlencode(
    basename($article['pdf_file'])
); ?>"

target="_blank"

rel="noopener"

class="
    btn
    btn-sm
    px-4
    py-2
    text-white
    rounded-pill
    shadow-sm
    fw-bold
    btn-theme-main
"

>

<i
class="
    fas
    fa-file-pdf
    me-2
"
></i>

PDF

</a>


<?php endif; ?>


<!-- ABSTRACT -->

<a

href="article?id=<?= (int)
    $article['id'];
?>"

class="
    btn
    btn-sm
    btn-outline-secondary
    px-4
    py-2
    rounded-pill
    fw-bold
"

style="
    border-width:
    2px;
"

>

Abstract

</a>


</div>

</div>

</div>


<?php endwhile; ?>


<?php else: ?>


<div
class="
    text-center
    py-5
"
>

<h5 class="text-muted">

No articles available
in this issue.

</h5>

</div>


<?php endif; ?>


<?php else: ?>


<div
class="
    text-center
    py-5
"
>


<i

class="
    fas
    fa-folder-open
    fa-4x
    mb-3
    opacity-25
"

style="
    color:
    var(--primary-color);
"

></i>


<h4 class="text-muted">

No issues published yet.

</h4>


</div>


<?php endif; ?>


<!-- ARCHIVES LINK -->

<div
class="
    col-12
    text-center
    mt-5
    pt-3
"
>


<div

class="
    p-4
    rounded-4
    shadow-sm
    text-white
"

style="
    background:
    linear-gradient(
        135deg,
        var(--dark-blue)
        0%,
        var(--primary-color)
        100%
    );
"

>


<h5
class="
    mb-2
    fw-bold
"
>

Archive Access

</h5>


<p
class="
    mb-0
    opacity-75
    small
"
>

Looking for older issues?

Explore our complete collection
in the

<a
href="archives"
class="
    text-white
    fw-bold
    text-decoration-underline
"
>

Archives

</a>

section.

</p>

</div>

</div>


</div>

</div>

</div>

</div>


<?php
include('includes/footer.php');
?>


</body>

</html>
