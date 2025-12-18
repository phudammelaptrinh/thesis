// Helper JavaScript cho URL động - sử dụng BASE_URL từ PHP
const APP_BASE_URL = window.APP_BASE_URL || '';

// Helper function tạo URL
function url(path) {
    path = path || '';
    if (path.startsWith('/')) {
        path = path.substring(1);
    }
    return APP_BASE_URL + (path ? '/' + path : '');
}

// Helper function tạo asset URL
function asset(path) {
    return APP_BASE_URL + '/public/' + (path.startsWith('/') ? path.substring(1) : path);
}

console.log('Base URL:', APP_BASE_URL);

