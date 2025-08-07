document.getElementById('studentBtn').addEventListener('click', function() {
    document.querySelector('form').removeEventListener('submit', teacherLoginHandler);
    document.querySelector('form').removeEventListener('submit', studentLoginHandler);
    
    document.querySelector('label[for="studentId"]').textContent = 'Enrollment Number';
    document.querySelector('label[for="phone"]').textContent = 'Phone Number';
    document.getElementById('studentId').setAttribute('type', 'text');
    document.getElementById('phone').setAttribute('type', 'text');
    document.getElementById('submitBtn').textContent = 'Submit';
    document.getElementById('studentId').value = '';
    document.getElementById('phone').value = '';

    document.querySelector('form').addEventListener('submit', studentLoginHandler);
    document.getElementById('studentId').addEventListener('input', validateInputsAndSubmit);
    document.getElementById('phone').addEventListener('input', validateInputsAndSubmit);
    document.getElementById('submitBtn').disabled = true;
});

document.getElementById('teacherBtn').addEventListener('click', function() {
    document.querySelector('form').removeEventListener('submit', studentLoginHandler);
    document.querySelector('form').removeEventListener('submit', teacherLoginHandler);
    
    document.querySelector('label[for="studentId"]').textContent = 'Username';
    document.querySelector('label[for="phone"]').textContent = 'Password';
    document.getElementById('studentId').setAttribute('type', 'text');
    document.getElementById('phone').setAttribute('type', 'password');
    document.getElementById('submitBtn').textContent = 'Login';
    document.getElementById('studentId').value = '';
    document.getElementById('phone').value = '';

    document.querySelector('form').addEventListener('submit', teacherLoginHandler);
});

function teacherLoginHandler(event) {
    event.preventDefault();
    const username = document.getElementById('studentId').value;
    const password = document.getElementById('email').value;

    if ((username === 'StudentZone2025' && password === 'Learn@123') || 
        (username === 'PrincipalDesk' && password === 'Lead@456')) {
        alert('Welcome Sir!');
        window.location.href = 'studentsecssion.html';
    } else {
        alert('You are not eligible for login.');
    }
}

function studentLoginHandler(event) {
    event.preventDefault();
    const enrollmentNumber = document.getElementById('studentId').value;
    const email = document.getElementById('email').value;

    const enrollmentPattern = /^\d{2}\d{4}\d{4}$/;
    if (!enrollmentPattern.test(enrollmentNumber)) {
        alert('Invalid enrollment number format. Please enter a 10-digit number in the correct format.');
        return;
    }

    const year = enrollmentNumber.substring(0, 2);
    const collegeCode = enrollmentNumber.substring(2, 6);
    const admissionNumber = enrollmentNumber.substring(6);

    if (year !== '22' || collegeCode !== '0135') {
        alert('Invalid enrollment number. Please enter a valid number.');
        return;
    }

    alert('Enrollment number and phone number validated.');
    window.location.href = 'otp.php'; // Redirect to OTP session page
}


function validateInputsAndSubmit() {
    const enrollmentNumber = document.getElementById('studentId').value;
    const phoneNumber = document.getElementById('phone').value;

    const enrollmentPattern = /^\d{2}\d{4}\d{4}$/;
    const phonePattern = /^[6-9]\d{9}$/;

    if (enrollmentPattern.test(enrollmentNumber) && phonePattern.test(phoneNumber)) {
        document.getElementById('submitBtn').disabled = false;
    } else {
        document.getElementById('submitBtn').disabled = true;
    }
}

document.getElementById('aboutBtn').onclick = function() {
    window.location.href = 'about.html';
};

document.getElementById('helpBtn').onclick = function() {
    window.location.href = 'help.html';
};

function clearLocalStorage() {
    localStorage.removeItem('enrollmentNumber');
    localStorage.removeItem('email');
}

function fetchStudentDetails() {
    const enrollmentNumber = document.getElementById("enrollment").value;
    
    if (enrollmentNumber.trim() === "") return; // Don't fetch if empty

    fetch("fetch_student.php?enrollment=" + enrollmentNumber)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("name").value = data.name;
                document.getElementById("phone").value = data.phone;
                document.getElementById("email").value = data.email;
                document.getElementById("branch").value = data.branch;
                document.getElementById("year").value = data.year;
                document.getElementById("location").value = data.location;
            } else {
                alert("No student found with this enrollment number.");
            }
        })
        .catch(error => console.error("Error fetching data:", error));
}
