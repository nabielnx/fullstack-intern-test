import './App.css'

function App() {
  function handleSubmit(event) {
    event.preventDefault()

    const formData = new FormData(event.currentTarget)

    const loginData = {
      email: formData.get('email'),
      loginAt: new Date().toISOString(),
    }

    localStorage.setItem('loginData', JSON.stringify(loginData))
    alert('Login berhasil!')
  }
  return (
    <main className="login-page">
      <div className="login-card">
        <div className="login-banner">
          <img src="/images/background.png" alt="Kegiatan perusahaan" />
        </div>

        <section className="login-content">
          <img
            className="login-logo"
            src="/images/logo.png"
            alt="Logo perusahaan"
          />

          <h1>Selamat Datang Kembali!</h1>

          <form onSubmit={handleSubmit}>
            <label htmlFor="email">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Enter your email"
              autoComplete="email"
              required
            />

            <label htmlFor="password">Password</label>
            <input
              id="password"
              name="password"
              type="password"
              placeholder="Enter your password"
              autoComplete="current-password"
              minLength={6}
              required
            />
            <a className='forgot-password' href='/forgot-password'>Lupa Password?</a>

            <button type="submit">Sign In</button>

          </form>

          <p className="signup-text">
            Belum punya akun? <a href="/register">Daftar di sini</a>
          </p>
          
        </section>
      </div>
    </main>
  )
}

export default App