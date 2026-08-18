#!/bin/bash

# ============================================
# اسکریپت آپلود خودکار به GitHub
# ============================================

echo "=========================================="
echo "  آپلود پروژه نوآوا به GitHub"
echo "=========================================="
echo ""

# وارد پوشه پروژه بشو
cd "G:/github/parsa/website" || {
    echo "خطا: نتوانستم پوشه پروژه رو پیدا کنم"
    exit 1
}

echo "✓ پوشه پروژه پیدا شد"
echo ""

# چک کن که Git نصب هست
echo "✓ چک کردن نصب Git..."
if ! command -v git &> /dev/null; then
    echo "خطا: Git نصب نیست"
    exit 1
fi

# چک کن که قبلا commit انجام شده
echo "✓ چک کردن وضعیت Git..."
if ! git rev-parse --is-inside-work-tree &> /dev/null; then
    echo "خطا: پوشه Git نیست"
    exit 1
fi

# دریافت آدرس ریپازیتوری از کاربر
echo "=========================================="
echo "لطفاً آدرس ریپازیتوری GitHub رو وارد کنید"
echo "مثال: https://github.com/AnishtayiN/nova-website.git"
echo "=========================================="
echo ""
read -p "آدرس ریپازیتوری: " repo_url

echo ""
echo "✓ آدرس ریپازیتوری دریافت شد: $repo_url"
echo ""

# اضاف کردن remote
echo "✓ اضافه کردن remote..."
git remote add origin "$repo_url" 2>/dev/null || {
    echo "   remote از قبل وجود داره، در حال آپدیت..."
    git remote set-url origin "$repo_url"
}

# تغییر برنچ به master
echo "✓ تنظیم برنچ..."
git branch -M master

# آپلود به GitHub
echo "✓ آپلود به GitHub..."
echo ""
git push -u origin master

if [ $? -eq 0 ]; then
    echo ""
    echo "=========================================="
    echo "✅ آپلود با موفقیت انجام شد!"
    echo "=========================================="
    echo ""
    echo "حالا میتونید ریپازیتوری رو اینجا ببینید:"
    echo "${repo_url%%.git}"
else
    echo ""
    echo "=========================================="
    echo "❌ خطا در آپلود"
    echo "=========================================="
    echo ""
    echo "اگر ریپازیتوری با README ساخته شده:"
    echo "  git pull origin master --allow-unrelated-histories"
    echo "  git push -u origin master"
fi

echo ""
