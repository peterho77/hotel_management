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
