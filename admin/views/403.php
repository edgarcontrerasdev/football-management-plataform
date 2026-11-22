<div class="error-page error-403">

    <div class="error-icon">
        <i class="fas fa-lock"></i>
    </div>

    <div class="error-code">403</div>

    <h2>Acceso no autorizado</h2>

    <p>
        No tienes permisos para acceder a este módulo
        o recurso.
    </p>

    <a href="index.php?page=<?= Auth::role() === 'LIGA' ? 'liga' : 'dashboard' ?>"
       class="btn-error">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>

</div>

<style>

.error-page {
    width: min(720px, 90%);
    min-height: 430px;
    margin: 40px auto;
    padding: 50px 40px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;

    background: #161616;
    border: 1px solid rgba(30, 144, 255, .35);
    border-radius: 12px;

    box-shadow:
        0 15px 40px rgba(0, 0, 0, .35),
        inset 0 0 30px rgba(30, 144, 255, .03);

    font-family: 'Roboto', Arial, sans-serif;
    box-sizing: border-box;
}

.error-icon {
    width: 76px;
    height: 76px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 18px;

    border-radius: 50%;

    background: rgba(30, 144, 255, .08);
    border: 1px solid rgba(30, 144, 255, .35);

    color: #1e90ff;
    font-size: 30px;

    box-shadow: 0 0 25px rgba(30, 144, 255, .08);
}

.error-code {
    margin: 0;

    font-size: 86px;
    line-height: 1;

    font-weight: 800;
    letter-spacing: 5px;

    color: #1e90ff;

    text-shadow:
        0 0 20px rgba(30, 144, 255, .15);
}

.error-page h2 {
    margin: 18px 0 10px;

    color: #ffffff;

    font-size: 25px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: .5px;
}

.error-page p {
    max-width: 500px;

    margin: 0 0 28px;

    color: #a9a9a9;

    font-size: 15px;
    line-height: 1.6;
}

.btn-error {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    padding: 11px 22px;

    background: #1e90ff;
    color: #ffffff;

    border: none;
    border-radius: 5px;

    font-size: 14px;
    font-weight: 600;

    text-decoration: none;

    transition:
        background .2s ease,
        transform .2s ease,
        box-shadow .2s ease;
}

.btn-error:hover {
    background: #0d6efd;
    color: #ffffff;

    transform: translateY(-2px);

    box-shadow: 0 7px 18px rgba(30, 144, 255, .25);
}

@media (max-width: 600px) {

    .error-page {
        min-height: 380px;
        padding: 40px 25px;
    }

    .error-code {
        font-size: 68px;
    }

    .error-page h2 {
        font-size: 21px;
    }

    .error-page p {
        font-size: 14px;
    }

}

</style>