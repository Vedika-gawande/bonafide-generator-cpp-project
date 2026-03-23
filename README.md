# 🎓 Bonafide Certificate Generator

An automated web-based system that allows students to apply for bonafide certificates online — eliminating paperwork and manual processes.

## 📌 About the Project

In most colleges, getting a bonafide certificate is a slow, manual process. This system digitizes the entire workflow — from student application to final certificate delivery — with role-based access for Students, HODs, and the Principal.

## 🔄 How It Works

1. 🧑‍🎓 **Student** registers, logs in, and submits a bonafide certificate request
2. 🧑‍💼 **HOD** reviews and approves/rejects the request
3. 👨‍💼 **Principal** gives final approval
4. 📄 Certificate is **auto-generated as a PDF**
5. 📧 Certificate is **emailed directly to the student**

## 👥 User Roles

| Role | Access |
|------|--------|
| Student | Register, login, apply for certificate, download certificate |
| HOD | View requests, approve/reject, sign certificates |
| Principal | Final approval, view all certificates |

## 🛠️ Tech Stack

| Technology | Usage |
|------------|-------|
| PHP | Backend logic & server-side processing |
| MySQL | Database management |
| FPDF | PDF certificate generation |
| HTML/CSS | Frontend UI |
| JavaScript | Client-side interactions |
| PHPMailer | Email delivery of certificates |

## ✨ Features

- 🔐 Secure login system for all three roles
- 📝 Online certificate request submission
- ✅ Multi-level approval workflow (HOD → Principal)
- 📄 Auto-generated PDF certificates
- 📧 Automatic email delivery to students
- 📊 Dashboard for each role

## 🚀 How to Run Locally

1. Clone the repository
```bash
   git clone https://github.com/Vedika-gawande/bonafide-generator-cpp-project.git
```
2. Move the project to your XAMPP `htdocs` folder
3. Import the database from the provided SQL file
4. Start Apache and MySQL in XAMPP
5. Open `http://localhost/bonafide-generator-cpp-project` in your browser

## 👩‍💻 Developer

**Vedika Gawande**  
B.Tech Computer Engineering @ GECA '28  
[LinkedIn](https://www.linkedin.com/in/vedika-gawande-71ab0031a) | [GitHub](https://github.com/Vedika-gawande)

