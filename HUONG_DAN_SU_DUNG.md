# 📖 HƯỚNG DẪN SỬ DỤNG HỆ THỐNG

## 🎯 HỆ THỐNG LUYỆN THI THPT QUỐC GIA MÔN TIN HỌC

---

## 📋 MỤC LỤC

1. [Tổng quan hệ thống](#tổng-quan-hệ-thống)
2. [Hướng dẫn cho Học sinh](#hướng-dẫn-cho-học-sinh)
3. [Hướng dẫn cho Giáo viên](#hướng-dẫn-cho-giáo-viên)
4. [Hướng dẫn cho Admin](#hướng-dẫn-cho-admin)
5. [Câu hỏi thường gặp](#câu-hỏi-thường-gặp)

---

## 🌟 TỔNG QUAN HỆ THỐNG

### Mục đích
Hệ thống hỗ trợ học sinh ôn luyện và giáo viên quản lý đề thi môn Tin học THPT Quốc gia.

### Các vai trò trong hệ thống:
- 👨‍🎓 **Học sinh**: Làm bài thi, xem kết quả, thống kê cá nhân
- 👨‍🏫 **Giáo viên**: Quản lý câu hỏi, tạo đề thi, theo dõi học sinh
- 👨‍💼 **Admin**: Quản trị toàn bộ hệ thống

### Truy cập hệ thống:
```
URL: http://localhost:8000
hoặc: http://127.0.0.1:8000
```

---

## 👨‍🎓 HƯỚNG DẪN CHO HỌC SINH

### 1. ĐĂNG KÝ TÀI KHOẢN

#### Bước 1: Truy cập trang chủ
- Mở trình duyệt và truy cập: `http://localhost:8000`
- Click nút **"Đăng ký"** trên thanh menu

#### Bước 2: Điền thông tin đăng ký
```
📝 Thông tin cần điền:
- Tên đăng nhập (Username) *
- Email *
- Mật khẩu *
- Xác nhận mật khẩu *
- Họ và tên *
- Lớp (VD: 12A1)
- Trường (VD: THPT Chu Văn An)
- Số điện thoại
- Ngày sinh
- Giới tính
```

#### Bước 3: Xác nhận và đăng nhập
- Click **"Đăng ký"**
- Hệ thống tự động đăng nhập sau khi đăng ký thành công

---

### 2. ĐĂNG NHẬP

#### Bước 1: Click "Đăng nhập" trên menu
#### Bước 2: Nhập thông tin
```
Username: hocsinh
Password: 123456
```
#### Bước 3: Click "Đăng nhập"
- Hệ thống sẽ chuyển đến màn hình "Danh sách đề thi"

---

### 3. XEM DANH SÁCH ĐỀ THI

#### Màn hình hiển thị:
```
┌─────────────────────────────────────────┐
│  🏠 DANH SÁCH ĐỀ THI                    │
├─────────────────────────────────────────┤
│  [Card 1]  [Card 2]  [Card 3]  [Card 4] │
│                                          │
│  Mỗi card hiển thị:                     │
│  - Tên đề thi                           │
│  - Số câu hỏi                           │
│  - Thời gian làm bài                    │
│  - Chủ đề                               │
│  - [Nút: BẮT ĐẦU LÀM BÀI]              │
└─────────────────────────────────────────┘
```

#### Thao tác:
1. Xem thông tin từng đề thi
2. Click **"Bắt đầu làm bài"** để vào thi

---

### 4. LÀM BÀI THI

#### Giao diện làm bài:
```
┌─────────────────────────────────────────────┐
│  ⏱️ Thời gian còn lại: 29:45               │
├─────────────────────────────────────────────┤
│  Câu 1/15: Trong Excel, hàm nào tính tổng?  │
│                                              │
│  ○ A. SUM()                                 │
│  ○ B. AVERAGE()                             │
│  ○ C. COUNT()                               │
│  ○ D. MAX()                                 │
│                                              │
│  [← Câu trước]  [Câu sau →]  [Nộp bài]     │
├─────────────────────────────────────────────┤
│  Bảng câu hỏi:                              │
│  [1] [2] [3] [4] [5] [6] [7] [8] ...       │
│   ✓   ✓   -   ✓   -   -   -   -            │
│  (Xanh: đã làm, Trắng: chưa làm)           │
└─────────────────────────────────────────────┘
```

#### Cách thức làm bài:

**Chọn đáp án:**
- Click vào đáp án A, B, C hoặc D
- Đáp án sẽ được highlight màu xanh

**Chuyển câu:**
- Click **"Câu sau"** để sang câu tiếp theo
- Click **"Câu trước"** để quay lại câu trước
- Hoặc click trực tiếp vào số câu ở bảng điều hướng

**Tính năng tự động lưu:**
- Hệ thống tự động lưu đáp án mỗi 30 giây
- Không lo mất dữ liệu nếu mất kết nối

**Nộp bài:**
1. Click nút **"Nộp bài"**
2. Xác nhận: "Bạn có chắc muốn nộp bài?"
3. Click **"Xác nhận"**

---

### 5. XEM KẾT QUẢ

#### Sau khi nộp bài:
```
┌─────────────────────────────────────────┐
│  🎉 KẾT QUẢ BÀI THI                     │
├─────────────────────────────────────────┤
│  Đề thi: Đề ôn tập Tin học cơ bản       │
│  Thời gian làm: 28 phút 35 giây         │
│                                          │
│  📊 ĐIỂM SỐ: 8.5/10                     │
│                                          │
│  ✓ Số câu đúng: 13/15                   │
│  ✗ Số câu sai: 2/15                     │
│                                          │
│  [Xem chi tiết đáp án]                  │
│  [Làm lại đề này]                       │
│  [Về danh sách đề thi]                  │
└─────────────────────────────────────────┘
```

#### Xem chi tiết đáp án:
- Click **"Xem chi tiết đáp án"**
- Hiển thị từng câu hỏi với:
  - ✓ Câu trả lời đúng (màu xanh)
  - ✗ Câu trả lời sai (màu đỏ)
  - Đáp án đúng được highlight

---

### 6. XEM LỊCH SỬ THI

#### Truy cập:
- Click menu **"Lịch sử thi"**

#### Thông tin hiển thị:
```
┌────────────────────────────────────────────────────┐
│  Mã bài  │  Đề thi       │  Điểm  │  Thời gian    │
├────────────────────────────────────────────────────┤
│  BL001   │  Đề ôn tập    │  8.5   │  14/12/2025   │
│  BL002   │  Đề thi thử   │  7.0   │  13/12/2025   │
│  BL003   │  Đề ôn tập    │  9.0   │  12/12/2025   │
└────────────────────────────────────────────────────┘
```

#### Thao tác:
- Click vào từng bài để xem chi tiết kết quả
- Xem đáp án đã chọn và đáp án đúng

---

### 7. THỐNG KÊ CÁ NHÂN

#### Truy cập:
- Click menu **"Thống kê cá nhân"**

#### Thông tin hiển thị:
```
┌─────────────────────────────────────────┐
│  📊 THỐNG KÊ CÁ NHÂN                    │
├─────────────────────────────────────────┤
│  Tổng số bài thi: 15                    │
│  Điểm trung bình: 7.8/10                │
│  Điểm cao nhất: 9.5/10                  │
│  Điểm thấp nhất: 5.5/10                 │
│                                          │
│  📈 Biểu đồ tiến độ                     │
│  [Hiển thị chart xu hướng điểm số]      │
│                                          │
│  📊 Phân bố chủ đề                      │
│  - Tin học cơ bản: 8/10                 │
│  - Lập trình: 7/10                      │
│  - Excel: 9/10                          │
└─────────────────────────────────────────┘
```

---

### 8. CÁC LƯU Ý QUAN TRỌNG

#### ⚠️ Khi làm bài thi:
1. **Không thoát khỏi trình duyệt** trong lúc thi
2. **Không reload trang** (F5) khi đang làm bài
3. **Không mở nhiều tab** cùng làm một đề
4. **Kiểm tra kết nối internet** trước khi thi
5. **Đọc kỹ đề** trước khi chọn đáp án
6. **Quản lý thời gian** - theo dõi đồng hồ đếm ngược

#### ✅ Các hành động được phép:
- Chuyển qua lại giữa các câu hỏi
- Sửa đáp án trước khi nộp bài
- Làm lại đề thi đã làm (nếu giáo viên cho phép)

#### ❌ Các hành động bị cấm:
- Copy/Paste nội dung câu hỏi
- Mở tab khác để tra cứu (hệ thống phát hiện gian lận)
- Chia sẻ đáp án với bạn bè
- Sử dụng nhiều tài khoản

---

## 👨‍🏫 HƯỚNG DẪN CHO GIÁO VIÊN

### 1. ĐĂNG NHẬP

```
Username: giaovien
Password: 123456
```

Sau khi đăng nhập, hệ thống chuyển đến **"Ngân hàng câu hỏi"**

---

### 2. QUẢN LÝ NGÂN HÀNG CÂU HỎI

#### Màn hình quản lý:
```
┌──────────────────────────────────────────────────┐
│  🏦 NGÂN HÀNG CÂU HỎI                            │
│  [+ Thêm câu hỏi]  [📤 Import]  [📥 Export]     │
├──────────────────────────────────────────────────┤
│  Mã CH  │  Nội dung  │  Chủ đề  │  Độ khó │ ...  │
├──────────────────────────────────────────────────┤
│  CH001  │  Trong...  │  Tin học │  Dễ     │ [⚙️]│
│  CH002  │  HTML là.. │  Tin học │  Dễ     │ [⚙️]│
└──────────────────────────────────────────────────┘
```

#### A. THÊM CÂU HỎI MỚI

**Bước 1:** Click nút **"+ Thêm câu hỏi"**

**Bước 2:** Điền thông tin
```
📝 Form nhập:
- Nội dung câu hỏi: (textarea) *
- Đáp án A: *
- Đáp án B: *
- Đáp án C: *
- Đáp án D: *
- Đáp án đúng: [○ A  ○ B  ○ C  ○ D] *
- Chủ đề: (dropdown) *
- Độ khó: [○ Dễ  ○ Trung bình  ○ Khó] *
```

**Bước 3:** Click **"Lưu câu hỏi"**

#### B. SỬA CÂU HỎI

**Bước 1:** Click nút ✏️ (Sửa) bên cạnh câu hỏi
**Bước 2:** Chỉnh sửa thông tin
**Bước 3:** Click **"Cập nhật"**

#### C. XÓA CÂU HỎI

**Bước 1:** Click nút 🗑️ (Xóa)
**Bước 2:** Xác nhận: "Bạn có chắc muốn xóa?"
**Bước 3:** Click **"Xóa"**

⚠️ **Lưu ý:** Không thể xóa câu hỏi đã được sử dụng trong đề thi

#### D. IMPORT CÂU HỎI HÀNG LOẠT

**Bước 1:** Chuẩn bị file JSON
```json
[
  {
    "NoiDung": "Trong Excel, hàm nào dùng để tính tổng?",
    "DapAnA": "SUM()",
    "DapAnB": "AVERAGE()",
    "DapAnC": "COUNT()",
    "DapAnD": "MAX()",
    "DapAn": "A",
    "DoKho": "Dễ",
    "ChuDe": "Tin học"
  },
  {
    "NoiDung": "HTML là viết tắt của gì?",
    "DapAnA": "HyperText Markup Language",
    "DapAnB": "High Tech Modern Language",
    "DapAnC": "Home Tool Markup Language",
    "DapAnD": "Hyperlinks and Text Markup Language",
    "DapAn": "A",
    "DoKho": "Dễ",
    "ChuDe": "Tin học"
  }
]
```

**Bước 2:** Click **"📤 Import"**
**Bước 3:** Chọn file JSON
**Bước 4:** Click **"Upload"**
**Bước 5:** Xem báo cáo import (số câu thành công/thất bại)

#### E. EXPORT CÂU HỎI

**Bước 1:** Click **"📥 Export"**
**Bước 2:** Chọn định dạng (JSON hoặc Excel)
**Bước 3:** File tự động download

---

### 3. QUẢN LÝ ĐỀ THI

#### Truy cập:
- Click menu **"Danh sách đề thi"**

#### Màn hình quản lý:
```
┌──────────────────────────────────────────────────────┐
│  📋 DANH SÁCH ĐỀ THI CỦA TÔI                        │
│  [+ Tạo đề mới] ▼                                    │
│    ├─ Tạo đề ngẫu nhiên                             │
│    └─ Tạo đề thủ công                               │
├──────────────────────────────────────────────────────┤
│  Mã đề│ Tên đề│ Số câu│ T.gian│ Lượt│ Trạng│ Thao │
├──────────────────────────────────────────────────────┤
│  DE001│ Đề... │  15   │ 30'   │  5  │ Hoạt │ [⚙️] │
│  DE002│ Đề... │  20   │ 45'   │  12 │ Hoạt │ [⚙️] │
└──────────────────────────────────────────────────────┘
```

#### A. TẠO ĐỀ THI NGẪU NHIÊN

**Bước 1:** Click **"Tạo đề thi"** → **"Tạo đề ngẫu nhiên"**

**Bước 2:** Điền thông tin
```
📝 Form tạo đề:
- Tên đề thi: * (VD: Đề ôn tập tuần 1)
- Chủ đề: * (Tin học, Lập trình, Excel...)
- Số lượng câu hỏi: * (5-50 câu)
- Thời gian làm bài: * (phút)
- Độ khó:
  ✓ Dễ: [__] câu
  ✓ Trung bình: [__] câu
  ✓ Khó: [__] câu
- Mô tả: (textarea)
```

**Bước 3:** Click **"Tạo đề"**
**Bước 4:** Hệ thống tự động chọn câu hỏi ngẫu nhiên

#### B. TẠO ĐỀ THI THỦ CÔNG

**Bước 1:** Click **"Tạo đề thi"** → **"Tạo đề thủ công"**

**Bước 2:** Điền thông tin cơ bản
```
- Tên đề thi: *
- Chủ đề: *
- Thời gian: *
- Mô tả:
```

**Bước 3:** Chọn câu hỏi
```
┌──────────────────────────────────────────┐
│  DANH SÁCH CÂU HỎI                       │
│  [Tìm kiếm...]                           │
├──────────────────────────────────────────┤
│  ☐ CH001: Trong Excel...                │
│  ☐ CH002: HTML là...                    │
│  ☐ CH003: Trong Python...               │
│  ...                                     │
│                                          │
│  Đã chọn: 0/50 câu                      │
│  [Thêm vào đề thi]                      │
└──────────────────────────────────────────┘
```

**Bước 4:** Tick chọn câu hỏi muốn thêm
**Bước 5:** Click **"Thêm vào đề thi"**
**Bước 6:** Click **"Lưu đề thi"**

#### C. SỬA ĐỀ THI

**Bước 1:** Click nút ✏️ (Sửa) bên cạnh đề thi
**Bước 2:** Chỉnh sửa:
- Tên đề thi
- Chủ đề
- Thời gian làm bài
- Mô tả
- Trạng thái (Kích hoạt/Vô hiệu)

⚠️ **Lưu ý:** Không thể sửa danh sách câu hỏi nếu đã có học sinh làm bài

**Bước 3:** Click **"Cập nhật"**

#### D. XEM CHI TIẾT ĐỀ THI

**Bước 1:** Click nút 👁️ (Xem) bên cạnh đề thi

**Thông tin hiển thị:**
```
┌──────────────────────────────────────────┐
│  📄 CHI TIẾT ĐỀ THI                     │
├──────────────────────────────────────────┤
│  Mã đề: DE001                            │
│  Tên: Đề ôn tập tuần 1                  │
│  Số câu: 15 câu                         │
│  Thời gian: 30 phút                     │
│  Lượt làm: 25                           │
│  Điểm TB: 7.8/10                        │
│                                          │
│  DANH SÁCH CÂU HỎI:                     │
│  1. Trong Excel... (Dễ)                 │
│  2. HTML là... (Dễ)                     │
│  3. Trong Python... (TB)                │
│  ...                                     │
└──────────────────────────────────────────┘
```

#### E. XÓA ĐỀ THI

**Bước 1:** Click nút 🗑️ (Xóa)
**Bước 2:** Xác nhận xóa

⚠️ **Lưu ý:** 
- Không thể xóa đề đã có học sinh làm bài
- Nếu muốn ẩn đề, hãy đổi trạng thái thành "Vô hiệu"

---

### 4. THỐNG KÊ LỚP HỌC

#### Truy cập:
- Click menu **"Thống kê lớp"**

#### Màn hình thống kê:
```
┌──────────────────────────────────────────────────┐
│  📊 THỐNG KÊ LỚP HỌC                             │
├──────────────────────────────────────────────────┤
│  [📈 45]        [🎯 7.8]        [✅ 80%]         │
│  Tổng HS       Điểm TB         Tỷ lệ đạt        │
├──────────────────────────────────────────────────┤
│  🏆 TOP 5 HỌC SINH GIỎI                         │
│  1. Nguyễn Văn A - Điểm TB: 9.5                 │
│  2. Trần Thị B - Điểm TB: 9.2                   │
│  3. Lê Văn C - Điểm TB: 9.0                     │
│  ...                                             │
├──────────────────────────────────────────────────┤
│  ⚠️ TOP 5 HỌC SINH CẦN HỖ TRỢ                  │
│  1. Phạm Văn D - Điểm TB: 4.5                   │
│  2. Hoàng Thị E - Điểm TB: 4.8                  │
│  ...                                             │
├──────────────────────────────────────────────────┤
│  📊 BIỂU ĐỒ PHÂN BỐ ĐIỂM                       │
│  [Hiển thị chart phân bố điểm số]               │
├──────────────────────────────────────────────────┤
│  📋 CHI TIẾT TẤT CẢ HỌC SINH                   │
│  [Bảng danh sách với điểm số chi tiết]          │
└──────────────────────────────────────────────────┘
```

#### Các chức năng:
- Xem tổng quan lớp học
- Xem top học sinh giỏi/yếu
- Xem phân bố điểm số
- Xuất báo cáo Excel

---

## 👨‍💼 HƯỚNG DẪN CHO ADMIN

### 1. ĐĂNG NHẬP

```
Username: admin
Password: admin123
```

Sau đăng nhập → **Dashboard**

---

### 2. DASHBOARD

#### Màn hình tổng quan:
```
┌──────────────────────────────────────────────────┐
│  📊 DASHBOARD - TỔNG QUAN HỆ THỐNG              │
├──────────────────────────────────────────────────┤
│  [👥 125]      [📝 50]       [❓ 500]           │
│  Users         Đề thi        Câu hỏi            │
│                                                  │
│  [📈 1,250]    [✅ 85%]      [⚡ 99.9%]        │
│  Lượt thi      Tỷ lệ đạt     Uptime             │
├──────────────────────────────────────────────────┤
│  📈 BIỂU ĐỒ THỐNG KÊ                            │
│  - Số lượt thi theo thời gian                   │
│  - Phân bố điểm số toàn hệ thống               │
│  - Top đề thi phổ biến                          │
└──────────────────────────────────────────────────┘
```

---

### 3. QUẢN LÝ NGƯỜI DÙNG

#### Truy cập:
- Click menu **"Quản lý người dùng"**

#### A. XEM DANH SÁCH NGƯỜI DÙNG

```
┌────────────────────────────────────────────────────┐
│  👥 QUẢN LÝ NGƯỜI DÙNG                            │
│  [+ Thêm người dùng]  [🔍 Tìm kiếm]              │
├────────────────────────────────────────────────────┤
│  ID│ Username│ Email      │ Role    │ T.thái│⚙️ │
├────────────────────────────────────────────────────┤
│  1 │ admin   │ admin@...  │ Admin   │ Hoạt  │[⚙️]│
│  2 │ giaovi..│ gv@...     │ GV      │ Hoạt  │[⚙️]│
│  3 │ hocsinh │ hs@...     │ HS      │ Hoạt  │[⚙️]│
└────────────────────────────────────────────────────┘
```

#### B. THÊM NGƯỜI DÙNG MỚI

**Bước 1:** Click **"+ Thêm người dùng"**

**Bước 2:** Điền form
```
📝 Thông tin tài khoản:
- Username: * (duy nhất)
- Email: * (duy nhất)
- Password: * (tối thiểu 6 ký tự)
- Role: * [○ Học sinh  ○ Giáo viên  ○ Admin]

📝 Thông tin cá nhân (tùy role):
- Họ và tên: *
- Ngày sinh:
- Giới tính: [○ Nam  ○ Nữ  ○ Khác]
- Lớp/Trường: (cho học sinh/giáo viên)
- Số điện thoại:
```

**Bước 3:** Click **"Tạo tài khoản"**

#### C. SỬA THÔNG TIN NGƯỜI DÙNG

**Bước 1:** Click nút ✏️ (Sửa)
**Bước 2:** Chỉnh sửa thông tin
**Bước 3:** Click **"Cập nhật"**

#### D. KHÓA/MỞ KHÓA TÀI KHOẢN

**Bước 1:** Click nút 🔒/🔓 (Khóa/Mở khóa)
**Bước 2:** Xác nhận

⚠️ **Lưu ý:** Tài khoản bị khóa không thể đăng nhập

#### E. XÓA TÀI KHOẢN

**Bước 1:** Click nút 🗑️ (Xóa)
**Bước 2:** Xác nhận xóa

⚠️ **Cảnh báo:** Xóa tài khoản sẽ xóa tất cả dữ liệu liên quan (bài làm, câu hỏi, đề thi...)

---

### 4. SAO LƯU & KHÔI PHỤC

#### Truy cập:
- Click menu **"Backup"**

#### A. TẠO BẢN SAO LƯU

**Bước 1:** Click **"+ Tạo bản sao lưu mới"**
**Bước 2:** Nhập mô tả (tùy chọn)
**Bước 3:** Click **"Bắt đầu sao lưu"**
**Bước 4:** Đợi quá trình hoàn tất (30s - 2 phút)

#### B. XEM DANH SÁCH BACKUP

```
┌──────────────────────────────────────────────────┐
│  💾 DANH SÁCH SAO LƯU                           │
│  [+ Tạo bản sao lưu mới]                        │
├──────────────────────────────────────────────────┤
│  Mã SL│ Tên file      │ Kích thước│ Ngày  │⚙️  │
├──────────────────────────────────────────────────┤
│  SL001│ backup_14...  │ 25.5 MB   │ 14/12 │[⬇️] │
│  SL002│ backup_13...  │ 24.8 MB   │ 13/12 │[⬇️] │
└──────────────────────────────────────────────────┘
```

#### C. TẢI XUỐNG BACKUP

**Bước 1:** Click nút ⬇️ (Download)
**Bước 2:** File tự động download

#### D. KHÔI PHỤC DỮ LIỆU

**Bước 1:** Click nút 🔄 (Khôi phục)
**Bước 2:** Xác nhận: "Thao tác này sẽ ghi đè dữ liệu hiện tại!"
**Bước 3:** Click **"Xác nhận khôi phục"**
**Bước 4:** Đợi quá trình hoàn tất

⚠️ **Cảnh báo:** Khôi phục sẽ ghi đè toàn bộ dữ liệu hiện tại!

#### E. XÓA BACKUP

**Bước 1:** Click nút 🗑️ (Xóa)
**Bước 2:** Xác nhận xóa

---

### 5. GIÁM SÁT HỆ THỐNG

#### Truy cập:
- Click menu **"Giám sát"**

#### Màn hình giám sát:
```
┌──────────────────────────────────────────────────┐
│  🖥️ GIÁM SÁT HỆ THỐNG                           │
├──────────────────────────────────────────────────┤
│  💾 Bộ nhớ: 45% (2.3 GB / 5 GB)                 │
│  [████████░░░░░░░░░░░]                          │
│                                                  │
│  💿 Ổ đĩa: 60% (120 GB / 200 GB)                │
│  [████████████░░░░░░░]                          │
│                                                  │
│  ⚡ CPU: 25%                                     │
│  [█████░░░░░░░░░░░░░░]                          │
│                                                  │
│  📊 Database: 1,250 records                     │
│  🔌 Connections: 15 active                      │
├──────────────────────────────────────────────────┤
│  📋 HOẠT ĐỘNG GẦN ĐÂY                           │
│  • 14/12 21:30 - User 'hocsinh' đăng nhập       │
│  • 14/12 21:25 - GV 'giaovien' tạo đề DE005     │
│  • 14/12 21:20 - Admin tạo backup SL003         │
└──────────────────────────────────────────────────┘
```

---

## ❓ CÂU HỎI THƯỜNG GẶP (FAQ)

### 1. Tôi quên mật khẩu, làm sao?
**Trả lời:** Liên hệ Admin để reset mật khẩu hoặc sử dụng chức năng "Quên mật khẩu" (nếu có).

### 2. Tại sao tôi không thể xóa câu hỏi?
**Trả lời:** Câu hỏi đã được sử dụng trong đề thi không thể xóa. Hãy xóa đề thi trước hoặc thay thế câu hỏi.

### 3. Làm sao để học sinh không thấy đáp án sau khi thi?
**Trả lời:** Admin/Giáo viên cấu hình trong phần "Cài đặt đề thi" → Tắt "Hiển thị đáp án sau khi nộp bài".

### 4. Hệ thống có phát hiện gian lận không?
**Trả lời:** Có. Hệ thống ghi nhận:
- Thoát tab/cửa sổ
- Copy/Paste
- Thoát chế độ fullscreen
- Thời gian làm bài bất thường

### 5. Tôi có thể làm lại đề thi đã làm không?
**Trả lời:** Có, nếu giáo viên cho phép. Mỗi lần làm sẽ tạo một bài làm mới.

### 6. Điểm số được tính như thế nào?
**Trả lời:** 
```
Điểm = (Số câu đúng / Tổng số câu) × 10
VD: 13 đúng / 15 câu = 8.67 điểm
```

### 7. Tôi có thể xuất kết quả ra Excel không?
**Trả lời:** Có. Giáo viên vào "Thống kê lớp" → Click "Xuất Excel".

### 8. Hệ thống có giới hạn số lượng đề thi/câu hỏi không?
**Trả lời:** Không giới hạn (phụ thuộc vào dung lượng server).

### 9. Tôi có thể import câu hỏi từ Word không?
**Trả lời:** Hiện tại chỉ hỗ trợ import JSON. Có thể chuyển đổi Word → JSON bằng công cụ online.

### 10. Làm sao liên hệ hỗ trợ kỹ thuật?
**Trả lời:** 
- Email: support@example.com
- Hotline: 0123-456-789
- Hoặc liên hệ Admin trong hệ thống

---

## 📞 LIÊN HỆ HỖ TRỢ

```
========================================
THÔNG TIN HỖ TRỢ KỸ THUẬT
========================================

📧 Email: support@example.com
📱 Hotline: 0123-456-789
💬 Zalo: 0123-456-789

🕐 Thời gian hỗ trợ:
   Thứ 2 - Thứ 6: 8:00 - 17:00
   Thứ 7 - CN: Theo lịch hẹn

🐛 Báo lỗi: 
   Email subject: [BUG] Mô tả lỗi

💡 Góp ý: 
   Email subject: [FEEDBACK] Ý kiến

========================================
```

---

**🎉 Chúc bạn sử dụng hệ thống hiệu quả!**

*Ngày cập nhật: 14/12/2025*
*Phiên bản: 1.0.0*
