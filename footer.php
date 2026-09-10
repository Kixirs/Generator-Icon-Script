<footer>
    <hr style="border: 0; border-top: 1px solid var(--input-border); margin-bottom: 20px;">
    <p>Створено студенткою 531 групи Руснак Аліною</p>
</footer>

    <div id="admin-loader">
        <div class="loader-content">

            <!-- USER -->
            <div class="user-icon">
                <i class="fa-solid fa-user"></i>
            </div>

            <!-- SHIELD -->
            <div class="shield-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <!-- TEXT -->
            <div class="loader-text">
                Завантаження Admin Panel...
            </div>
        </div>
    </div>

<style>

#admin-loader {
    display: none;
    position: fixed;
    inset: 0;
    background: #050505;
    z-index: 999999;

    justify-content: center;
    align-items: center;

    overflow: hidden;
}

/* Фоновий glow */
#admin-loader::before {
    content: "";
    position: absolute;

    width: 500px;
    height: 500px;

    background: radial-gradient(circle,
        rgba(234,204,128,0.15) 0%,
        transparent 70%);

    animation: pulseBg 3s infinite;
}

.loader-content {
    position: relative;

    display: flex;
    flex-direction: column;
    align-items: center;

    gap: 20px;

    z-index: 2;
}

/* USER ICON */
.user-icon {
    font-size: 85px;
    color: white;

    opacity: 0;

    transform: translateY(-40px) scale(0.7);

    animation:
        userAppear 1s ease forwards,
        floating 3s ease-in-out infinite 1s;
}

/* SHIELD */
.shield-icon {
    position: absolute;

    font-size: 45px;

    color: #EACC80;

    right: -30px;
    bottom: 15px;

    opacity: 0;

    transform: scale(0);

    animation:
        shieldAppear 0.8s ease forwards 0.9s,
        shieldGlow 2s infinite 1.7s;
}

/* TEXT */
.loader-text {
    margin-top: 40px;

    color: rgba(255,255,255,0.85);

    font-size: 20px;
    letter-spacing: 1px;

    opacity: 0;

    animation: textFade 1s ease forwards 1.5s;
}

/* USER APPEAR */
@keyframes userAppear {

    0% {
        opacity: 0;
        transform: translateY(-40px) scale(0.7);
    }

    60% {
        opacity: 1;
        transform: translateY(10px) scale(1.08);
    }

    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* SHIELD APPEAR */
@keyframes shieldAppear {

    0% {
        opacity: 0;
        transform: scale(0) rotate(-180deg);
    }

    70% {
        opacity: 1;
        transform: scale(1.2) rotate(10deg);
    }

    100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}

/* FLOATING */
@keyframes floating {

    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-8px);
    }

    100% {
        transform: translateY(0px);
    }
}

/* SHIELD GLOW */
@keyframes shieldGlow {

    0% {
        text-shadow: 0 0 5px #EACC80;
    }

    50% {
        text-shadow:
            0 0 15px #EACC80,
            0 0 30px #EACC80;
    }

    100% {
        text-shadow: 0 0 5px #EACC80;
    }
}

/* TEXT */
@keyframes textFade {

    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* BACKGROUND PULSE */
@keyframes pulseBg {

    0% {
        transform: scale(1);
        opacity: 0.4;
    }

    50% {
        transform: scale(1.15);
        opacity: 0.8;
    }

    100% {
        transform: scale(1);
        opacity: 0.4;
    }
}
</style>



<script>

document.addEventListener('DOMContentLoaded', () => {

    const adminBtn = document.getElementById('adminEnterBtn');
    const loader = document.getElementById('admin-loader');

    if(adminBtn){
        adminBtn.addEventListener('click', function(e){

            e.preventDefault();
            loader.style.display = 'flex';
            setTimeout(() => {

                window.location.href = 'admin.php';

            }, 3500);

        });

    }

});

</script>

</body>
</html>