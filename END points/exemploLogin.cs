using System;
using System.Windows.Forms;
using VoidCorporation;

namespace SeuProjeto
{
    public partial class LoginForm : Form
    {
        private readonly ApiAuth auth;

        public LoginForm()
        {
            InitializeComponent();
            auth = new ApiAuth("https://snow-hornet-956876.hostingersite.com/login.php");
        }

        private async void btnLogin_Click(object sender, EventArgs e)
        {
            btnLogin.Enabled = false;
            var result = await auth.Login(txtUsername.Text, txtPassword.Text);
            btnLogin.Enabled = true;

            MessageBox.Show(result.Message, result.Success ? "Sucesso" : "Erro");

            if (result.Success)
            {
                var mainForm = new MainForm(result);
                mainForm.Show();
                Hide();
            }
        }
    }
}