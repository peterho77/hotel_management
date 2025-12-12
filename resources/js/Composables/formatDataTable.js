import {
    formatLabel,
    formatDateVN,
    isDateString,
} from "@/Composables/formatData";

// data table prime vue

export function formatDataTable(data) {

    // Nếu là mảng → xử lý từng object
    if (Array.isArray(data)) {
        return data.map(item => formatDataTable(item));
    }

    // Nếu là object → duyệt từng key
    if (data !== null && typeof data === "object") {
        let result = {};

        for (const [key, value] of Object.entries(data)) {

            // Nếu là date string → format
            if (isDateString(value)) {
                result[key] = formatDateVN(value);
            }
            // Nếu là object → đệ quy
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

    // Giá trị không phải object → return luôn
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
