const elementModules = import.meta.glob("./elements/**/*.{jpg,jpeg,png,webp}", {
    eager: true,
    as: "url",
});

// Ảnh dùng cho TRANG CHÍNH (không include album film của gallery)
// - Bỏ các frame trang trí
// - Bỏ toàn bộ ảnh trong thư mục film (dành riêng cho /image-gallery)
export const allImageUrls: string[] = Object.values(elementModules).filter(
    (url) =>
        typeof url === "string" &&
        !url.includes("film-strip-graphic-element-frame") &&
        !url.includes("/film/"),
) as string[];

