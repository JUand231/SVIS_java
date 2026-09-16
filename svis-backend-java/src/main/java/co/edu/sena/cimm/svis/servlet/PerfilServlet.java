package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.config.AppContext;
import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.model.Ficha;
import co.edu.sena.cimm.svis.model.Usuario;
import co.edu.sena.cimm.svis.repository.FichaRepository;
import co.edu.sena.cimm.svis.repository.UsuarioRepository;
import co.edu.sena.cimm.svis.util.JsonUtil;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

@WebServlet("/api/perfil")
public class PerfilServlet extends HttpServlet {

    private UsuarioRepository usuarios;
    private FichaRepository fichas;

    @Override
    public void init() {
        usuarios = AppContext.get().getUsuarioRepository();
        fichas = AppContext.get().getFichaRepository();
    }

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");
        String idParam = req.getParameter("usuarioId");
        if (idParam == null || idParam.trim().isEmpty()) {
            resp.setStatus(400);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", "usuarioId requerido")));
            return;
        }
        try {
            Long id = Long.parseLong(idParam.trim());
            Usuario u = usuarios.obtenerPorId(id);
            if (u == null) {
                resp.setStatus(404);
                resp.getWriter().write(JsonUtil.toJson(new ApiError("NO_ENCONTRADO", "Usuario no existe")));
                return;
            }
            u.setContrasenia(null);
            Map<String, Object> out = new HashMap<>();
            out.put("usuario", u);
            if (u.getFicha_id() != null) {
                try {
                    Ficha f = fichas.obtenerPorId(u.getFicha_id());
                    out.put("ficha", f);
                } catch (Exception e) {
                    out.put("ficha", null);
                }
            }
            resp.setStatus(200);
            resp.getWriter().write(JsonUtil.toJson(out));
        } catch (NumberFormatException e) {
            resp.setStatus(400);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ID_INVALIDO", "usuarioId debe ser numero")));
        } catch (Exception e) {
            resp.setStatus(500);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }
}
