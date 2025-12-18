// Cấu hình BASE_URL từ PHP
const BASE_URL = typeof PHP_BASE_URL !== 'undefined' ? PHP_BASE_URL : '';

// Helper function
function url(path = '') {
    if (path.startsWith('/')) path = path.substring(1);
    return BASE_URL + (path ? '/' + path : '');
}

function asset(path) {
    return url('public/' + path);
}

// Export cho sử dụng toàn cục
window.url = url;
window.asset = asset;
window.BASE_URL = BASE_URL;
