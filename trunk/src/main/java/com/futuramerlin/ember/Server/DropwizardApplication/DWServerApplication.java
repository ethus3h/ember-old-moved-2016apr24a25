package com.example.helloworld;

import io.dropwizard.Application;
import io.dropwizard.setup.Bootstrap;
import io.dropwizard.setup.Environment;
import com.futuramerlin.ember.Server.DropwizardApplication.resources.HelloWorldResource;
import com.futuramerlin.ember.Server.DropwizardApplication.health.TemplateHealthCheck;

public class EmberServerApplication extends Application<EmberServerApplication> {
    public static void main(String[] args) throws Exception {
        new EmberServerApplication().run(args);
    }

    @Override
    public String getName() {
        return "hello-world";
    }

    @Override
    public void initialize(Bootstrap<EmberServerApplication> bootstrap) {
        // nothing to do yet
    }

    @Override
    public void run(EmberServerApplication configuration,
                    Environment environment) {
        // nothing to do yet
    }

}