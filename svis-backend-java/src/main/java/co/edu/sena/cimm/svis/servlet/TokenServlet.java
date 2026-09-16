package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.dto.TokenGenerarRequest;
import co.edu.sena.cimm.svis.service.TokenService;
import co.edu.sena.cimm.svis.util.JsonUtil;
import java.io.BufferedReader;
import java.io.IOException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/api/tokens/generar")
public class TokenServlet extends HttpServlet {

    private TokenService token;

    @Override
    public void init() {
        token = new TokenService();

    }

    // GET /api/tokens/generar?encuestaId=1 -> lista de tokens (para mostrar OTP en admin)
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");
        try {
            String idParam = req.getParameter("encuestaId");
            if (idParam == null || idParam.trim().isEmpty()) {
                resp.setStatus(400);
                resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", "encuestaId requerido")));
                return;
            }
            Long id = Long.parseLong(idParam.trim());
            resp.setStatus(200);
            resp.getWriter().write(JsonUtil.toJson(token.listarPorEncuesta(id)));
        } catch (NumberFormatException e) {
            resp.setStatus(400);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ID_INVALIDO", "encuestaId debe ser numero")));
        } catch (Exception e) {
            resp.setStatus(500);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
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
            TokenGenerarRequest in = JsonUtil.fromJson(sb.toString(), TokenGenerarRequest.class);

            if (in == null || in.encuestaId == null) {
                resp.setStatus(400);
                resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", "encuestaId requeridos")));
                return;
            }
            int creados = token.generarParaEncuesta(in.encuestaId, in.diasExpiracion);
            resp.setStatus(200);
            java.util.Map<String, Object> out = new java.util.HashMap<>();
            out.put("creados", creados);
            resp.getWriter().write(JsonUtil.toJson(out));

        } catch (RuntimeException e) {
            resp.setStatus(400);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(500);
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }

    }
}
