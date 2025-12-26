export function formatLabel(str) {
    if (!str) return "";
    str = String(str);
    str = str.replace(/[-_]/g, " ");
    return str
        .split(" ")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
}

export function formatDateVN(input) {
    if (!input) return "";

    // Nếu có microsecond hoặc dạng ISO, loại bỏ phần microsecond
    input = input.replace(/\.\d+Z$/, "Z");

    const date = new Date(input);

    if (isNaN(date)) return "";

    // Kiểm tra xem có thời gian hay không
    const hasTime = /\d{2}:\d{2}:\d{2}/.test(input);

    return new Intl.DateTimeFormat("vi-VN", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: hasTime ? "2-digit" : undefined,
        minute: hasTime ? "2-digit" : undefined,
        second: hasTime ? "2-digit" : undefined,
        hour12: false,
        timeZone: "Asia/Ho_Chi_Minh",
    }).format(date);
}

// format vn currency
export function formatCurrency(value) {
    if (!value) return '0 ₫';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);
}

export function isDateString(value) {
    if (typeof value !== "string") return false;

    // Ngày dạng YYYY-MM-DD
    const dateOnly = /^\d{4}-\d{2}-\d{2}$/;

    // ISO: YYYY-MM-DDTHH:mm:ss.sssZ
    const isoDate = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/;

    return dateOnly.test(value) || isoDate.test(value);
}
