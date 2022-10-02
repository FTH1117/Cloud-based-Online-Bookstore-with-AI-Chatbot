<?php
defined('admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','caption','date_uploaded'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (title LIKE :search OR caption LIKE :search OR full_path LIKE :search) ' : '';
// Retrieve the total number of media
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM media ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$media_total = $stmt->fetchColumn();
// SQL query to get all media from the "media" table
$stmt = $pdo->prepare('SELECT * FROM media ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Determine the URL
$url = 'index.php?page=media&search=' . $search;
?>
<?=template_admin_header('Media', 'media')?>

<div class="content-title">
    <h2>Media</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>


<div class="content-header responsive-flex-column pad-top-5">
    <a href="#" class="btn upload">Upload</a>
    <form action="" method="get">
        <input type="hidden" name="page" value="media">
        <a href="<?=$url?>&order_by=<?=$order_by?>&order=<?=$order=='ASC'?'DESC':'ASC'?>"><i class="fa-solid fa-arrow-<?=$order=='ASC'?'up':'down'?>-wide-short"></i></a>
        <select name="order_by" onchange="this.form.submit()">
            <option value="" disabled>Order By</option>
            <option value="id"<?=$order_by=='id'?' selected':''?>>ID</option>
            <option value="title"<?=$order_by=='title'?' selected':''?>>Title</option>
            <option value="caption"<?=$order_by=='caption'?' selected':''?>>Caption</option>
            <option value="date_uploaded"<?=$order_by=='date_uploaded'?' selected':''?>>Date Uploaded</option>
        </select>
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search media..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block media-page">
    <div class="media">
        <?php foreach ($media as $m): ?>
        <a href="#" class="image" data-id="<?=$m['id']?>" data-full-path="<?=$m['full_path']?>" data-title="<?=$m['title']?>" data-caption="<?=$m['caption']?>" data-date-uploaded="<?=date('Y-m-d\TH:i', strtotime($m['date_uploaded']))?>">
            <img src="../<?=$m['full_path']?>" alt="<?=$m['caption']?>" loading="lazy">
        </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($media_total / $results_per_page) == 0 ? 1 : ceil($media_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $media_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer('initMedia()')?>