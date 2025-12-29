import {
    formatLabel,
    formatCurrency,
    formatDateVN,
    isDateString,
} from "@/Composables/formatData";

// data table prime vue

export function formatDataTable(data) {
    // 1. Nếu là mảng → xử lý đệ quy từng phần tử
    if (Array.isArray(data)) {
        return data.map((item) => formatDataTable(item));
    }

    // 2. Nếu là object → duyệt từng key
    if (data !== null && typeof data === "object") {
        let result = {};

        for (const [key, value] of Object.entries(data)) {
            // Kiểm tra key kết thúc bằng 'price' (ví dụ: cost_price, sellingPrice)
            // Và đảm bảo value là số hoặc chuỗi số để tránh format nhầm object
            if (
                key.toLowerCase().endsWith("price") &&
                (typeof value === "number" ||
                    (typeof value === "string" && !isNaN(value)))
            ) {
                result[key] = formatCurrency(value);
            }

            // Nếu là date string → format Date
            else if (isDateString(value)) {
                result[key] = formatDateVN(value);
            }
            // Nếu là object con → đệ quy tiếp
            else if (typeof value === "object" && value !== null) {
                result[key] = formatDataTable(value);
            }
            // Giá trị bình thường → giữ nguyên
            else {
                result[key] = value;
            }
        }
        return result;
    }

    // 3. Giá trị primitive (số, chuỗi thường...) → return luôn
    return data;
}

export function getColumns(data, hiddenColumns = []) {
    let keys = Object.keys(data);

    let columns = keys.map((key) => ({
        field: key,
        header: formatLabel(key),
    }));

    return columns.filter((item) => !hiddenColumns.includes(item.field));
}

export function getDetailRows(data, hiddenRows = []) {
    return Object.fromEntries(
        Object.entries(data)
            // loại bỏ key nằm trong hiddenRows
            .filter(([key]) => !hiddenRows.includes(key))
            // format date string nếu cần
            .map(([key, value]) => {
                if (isDateString(value)) {
                    return [key, formatDateVN(value)];
                }
                return [key, value];
            })
    );
}
