const elementModules = import.meta.glob("./elements/**/*.{jpg,jpeg,png,webp}", {
    eager: true,
    as: "url",
});

// Lấy toàn bộ URL ảnh, bỏ qua frame trang trí nếu có
export const allImageUrls: string[] = Object.values(elementModules).filter(
    (url) =>
        typeof url === "string" &&
        !url.includes("film-strip-graphic-element-frame"),
) as string[];

