<div id = "search">
    <form action="resultPage.php" method="get" class="searchForm">
        <input class="searchText" type="text" placeholder="find your love!" name="searchTerm" required/>
        <input class="searchSubmit" type="submit" value="search"> 
        <a href="../view/advancedSearchPage.php"> advanced </a>
        <a href="../util/logout.php" style="float:right;"> log out</a>
        <span style="color:#333;float:right;position:relative;top:4px;right:6px;">
            <?php echo $self->screenName ?>
        </span>
    </form>
   
</div>