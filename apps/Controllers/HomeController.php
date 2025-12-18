<?php
class HomeController
{
    public function dashboard()
    {
        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/layouts/section.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
