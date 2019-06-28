<style>
    #docs-nav .collapse {
        margin-left: 1rem;
    }
    #docs-nav a{
        position: relative;
        padding-left: 1.5rem;
    }


    #docs-nav a:before,
    #docs-nav a.folder-open:before{
        font-family: Pixelion;
        position: absolute;
        left:0;
        top:7px;
    }
    #docs-nav a:before{
        content:"\f093";

    }
    #docs-nav a.folder-open:before{
        content:"\f023";

    }
</style>
<nav class="nav flex-column" id="docs-nav">
    <?= $this->context->recursive($model); ?>
</nav>

