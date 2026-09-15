package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.dto.LoginAdminRequest;
import co.edu.sena.cimm.svis.model.Usuario;
import co.edu.sena.cimm.svis.service.AuthService;
import co.edu.sena.cimm.svis.util.JsonUtil;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.BufferedReader;
import java.io.IOException;

@WebServlet("/api/login-admin")
public class LoginAdminServlet extends HttpServlet {

    private AuthService auth;

    @Override
    public void init() {
        auth = new AuthService();
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");

        try {

            BufferedReader r = req.getReader();
            StringBuilder sb = new StringBuilder();
            String linea;
            while ((linea = r.readLine()) != null) {
                sb.append(linea);
            }
            LoginAdminRequest in = JsonUtil.fromJson(sb.toString(), LoginAdminRequest.class);

            if (in == null || in.username == null || in.contrasenia == null) {
                resp.setStatus(400);
                resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", "username y contrasenia requeridos")));
                return;
            }

            Usuario u = auth.loginAdmin(in.username, in.contrasenia);
            u.setContrasenia(null);

            resp.setStatus(200);
            resp.getWriter().write(JsonUtil.toJson(u));

        } catch (RuntimeException e) {
            resp.setStatus(401);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("NO_AUTH", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(500);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }
}
