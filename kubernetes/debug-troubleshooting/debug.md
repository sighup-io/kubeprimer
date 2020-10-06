## Debug and troubleshooting

Deploy everything with the following command:

```bash

    kubectl apply -f ns.yml

    kubectl apply -f .

    kubectl apply -f ../volumes/kubeprimer-db-persistentvolumeclaim.yaml

    kubectl apply -f ../configmaps/powerapp-configmap.yaml

    kubectl config set-context --current --namespace=debug

```

Once is deployed, check that everything is working fine:

1. Are all pod running?
2. Are the services configured correctly?
3. How do you start debugging?
